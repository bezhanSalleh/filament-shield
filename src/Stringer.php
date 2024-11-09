<?php

/** @noinspection PhpSuspiciousNameCombinationInspection */

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield;

use Illuminate\Support\Traits\Conditionable;

class Stringer
{
    use Conditionable;

    protected string $content;

    protected string $filePath;

    protected int $baseIndentLevel = 0; // Track the base indentation level

    protected int $currentIndentLevel = 0; // Track the current indentation level

    protected bool $addNewLine = false; // Track whether to add a new line

    public static function for(string $filePath): static
    {
        return app(static::class, ['filePath' => $filePath]);
    }

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->content = file_get_contents($filePath) ?: '';
    }

    protected function findLine(string $needle): ?array
    {
        // Search for the needle and return the line and its indentation
        $startPos = strpos($this->content, $needle);
        if ($startPos === false) {
            return null; // Not found
        }

        // Get the start of the line and calculate indentation
        $lineStartPos = strrpos(substr($this->content, 0, $startPos), PHP_EOL) ?: 0;
        $lineEndPos = strpos($this->content, PHP_EOL, $startPos) ?: strlen($this->content);

        $line = substr($this->content, $lineStartPos, $lineEndPos - $lineStartPos);
        $indentation = preg_replace('/\S.*/', '', $line); // Capture indentation

        return [
            'start' => $lineStartPos,
            'end' => $lineEndPos,
            'indentation' => $indentation,
        ];
    }

    public function prepend(string $needle, string $contentToPrepend, bool $beforeBlock = false): static
    {
        if (! $this->contains($needle)) {
            return $this; // Needle not found
        }

        if ($beforeBlock) {
            // Find the starting position of the method
            $startPos = strpos($this->content, $needle);
            if ($startPos === false) {
                return $this; // Needle not found
            }

            // Find the opening parenthesis position
            $openingParenPos = strpos($this->content, '(', $startPos);
            if ($openingParenPos === false) {
                return $this; // No opening parenthesis found
            }

            // Find the closing parenthesis
            $closingParenPos = $this->findClosingParen($openingParenPos);
            if (is_null($closingParenPos)) {
                return $this; // No closing parenthesis found
            }

            // Get the line indentation
            $lineInfo = $this->findLine($needle);
            $indentation = $lineInfo['indentation'] . $this->getIndentation();

            // Format the new content based on the newLine flag
            $formattedReplacement = $this->addNewLine
                ? PHP_EOL . $indentation . trim($contentToPrepend)
                : $indentation . trim($contentToPrepend);

            $this->addNewLine = false; // Reset flag

            // Insert the formatted replacement before the opening parenthesis
            $this->content = substr_replace($this->content, $formattedReplacement, $openingParenPos, 0);
        } else {
            // Normal prepend logic
            if ($lineInfo = $this->findLine($needle)) {
                // Prepend the content with proper indentation
                $newContent = $lineInfo['indentation'] . $this->getIndentation() . trim($contentToPrepend);
                if ($this->addNewLine) {
                    $newContent = PHP_EOL . $newContent;
                    $this->addNewLine = false; // Reset the flag
                }
                $this->content = substr_replace(
                    $this->content,
                    $newContent,
                    $lineInfo['start'],
                    0
                );
            }
        }

        return $this;
    }

    public function append(string $needle, string $contentToAppend, bool $afterBlock = false): static
    {
        if (! $this->contains($needle)) {
            return $this; // Needle not found
        }

        if ($afterBlock) {
            // Find the starting position of the method
            $startPos = strpos($this->content, $needle);
            if ($startPos === false) {
                return $this; // Needle not found
            }

            // Find the opening parenthesis position
            $openingParenPos = strpos($this->content, '(', $startPos);
            if ($openingParenPos === false) {
                return $this; // No opening parenthesis found
            }

            // Find the closing parenthesis
            $closingParenPos = $this->findClosingParen($openingParenPos);
            if (is_null($closingParenPos)) {
                return $this; // No closing parenthesis found
            }

            // Get the line indentation
            $lineInfo = $this->findLine($needle);
            $indentation = $lineInfo['indentation'] . $this->getIndentation();

            // Format the new content based on the newLine flag
            $formattedReplacement = $this->addNewLine
                ? $indentation . trim($contentToAppend) . PHP_EOL
                : $indentation . trim($contentToAppend);

            $this->addNewLine = false; // Reset flag

            // If the closing parenthesis has a semicolon, move it to a new line with indentation
            if ($this->content[$closingParenPos + 1] === ';') {
                $this->content = substr_replace(
                    $this->content,
                    PHP_EOL . $indentation . ';',
                    $closingParenPos + 1,
                    0
                );
                $closingParenPos += strlen(PHP_EOL . $indentation . ';'); // Adjust closing position
            }

            // Insert the formatted replacement after the closing parenthesis
            $this->content = substr_replace($this->content, $formattedReplacement, $closingParenPos + 1, 0);
        } else {
            // Normal append logic
            if ($lineInfo = $this->findLine($needle)) {
                // Append the content with proper indentation
                $newContent = $lineInfo['indentation'] . $this->getIndentation() . trim($contentToAppend);
                if ($this->addNewLine) {
                    $newContent = $newContent . PHP_EOL;
                    $this->addNewLine = false; // Reset the flag
                }
                $this->content = substr_replace(
                    $this->content,
                    $newContent,
                    $lineInfo['end'],
                    0
                );
            }
        }

        return $this;
    }

    protected function findClosingParen(int $openingParenPos): ?int
    {
        $stack = 0;
        $length = strlen($this->content);

        for ($i = $openingParenPos; $i < $length; $i++) {
            if ($this->content[$i] === '(') {
                $stack++;
            } elseif ($this->content[$i] === ')') {
                $stack--;
                if ($stack === 0) {
                    return $i; // Found the closing parenthesis
                }
            }
        }

        return null; // Closing parenthesis not found
    }

    public function replace(string $needle, string $replacement): static
    {
        if ($lineInfo = $this->findLine($needle)) {
            // Replace the entire line containing the needle
            $this->content = substr_replace(
                $this->content,
                $lineInfo['indentation'] . trim($replacement),
                $lineInfo['start'],
                strlen(substr($this->content, $lineInfo['start'], $lineInfo['end'] - $lineInfo['start']))
            );
        }

        return $this;
    }

    public function newLine(): static
    {
        $this->addNewLine = true; // Set the flag to add a new line

        return $this;
    }

    public function indent(int $level): static
    {
        $this->currentIndentLevel += $level;

        return $this;
    }

    public function getIndentation(): string
    {
        return str_repeat(' ', $this->baseIndentLevel + $this->currentIndentLevel);
    }

    public function contains(string $needle): bool
    {
        // Check if the needle has a wildcard for partial matching
        $isPartialMatch = str_starts_with($needle, '*') || str_ends_with($needle, '*');

        if ($isPartialMatch) {
            // Strip the `*` characters for partial matching
            $needle = trim($needle, '*');

            return (bool) preg_match('/' . preg_quote($needle, '/') . '/', $this->content);
        } else {
            // Perform an exact search
            return str_contains($this->content, $needle);
        }
    }

    public function save(): bool
    {
        return (bool) file_put_contents($this->filePath, $this->content);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function prependBeforeLast(string $needle, string $replacement): static
    {
        $lastPos = strrpos($this->content, $needle);

        if ($lastPos !== false) {
            $lineStartPos = strrpos(substr($this->content, 0, $lastPos), PHP_EOL) ?: 0;
            $line = substr($this->content, $lineStartPos, $lastPos - $lineStartPos);

            preg_match('/^\s*/', $line, $matches);
            $originalIndentation = $matches[0] ?? '';

            $formattedReplacement = $this->getIndentation() . trim($replacement);
            if ($this->addNewLine) {
                $formattedReplacement = PHP_EOL . $formattedReplacement . PHP_EOL;
            }

            $this->addNewLine = false;

            $this->content = substr_replace($this->content, $originalIndentation . $formattedReplacement, $lineStartPos, 0);
        }

        return $this;
    }

    protected function findMethodDeclaration(string $needle): ?array
    {
        $lines = explode(PHP_EOL, $this->content);
        $normalizedNeedle = preg_replace('/\s+/', ' ', trim($needle));

        for ($i = 0; $i < count($lines); $i++) {
            $currentLine = trim($lines[$i]);
            $nextLine = isset($lines[$i + 1]) ? trim($lines[$i + 1]) : '';

            // Check if current line contains the method declaration
            // and next line contains the opening brace
            if (str_contains(preg_replace('/\s+/', ' ', $currentLine), $normalizedNeedle)
                && str_contains($nextLine, '{')) {

                $startPos = 0;
                for ($j = 0; $j < $i; $j++) {
                    $startPos += strlen($lines[$j]) + strlen(PHP_EOL);
                }

                $endPos = $startPos + strlen($lines[$i]) + strlen(PHP_EOL) + strlen($lines[$i + 1]);
                $indentation = preg_replace('/\S.*/', '', $lines[$i]);

                // Find the closing brace position
                $braceLevel = 0;
                $methodEndLine = $i + 1;
                for ($j = $i + 1; $j < count($lines); $j++) {
                    if (str_contains($lines[$j], '{')) {
                        $braceLevel++;
                    }
                    if (str_contains($lines[$j], '}')) {
                        $braceLevel--;
                        if ($braceLevel === 0) {
                            $methodEndLine = $j;

                            break;
                        }
                    }
                }

                $methodEndPos = $startPos;
                for ($j = $i; $j <= $methodEndLine; $j++) {
                    $methodEndPos += strlen($lines[$j]) + strlen(PHP_EOL);
                }

                return [
                    'start' => $startPos,
                    'end' => $endPos,
                    'method_end' => $methodEndPos,
                    'indentation' => $indentation,
                    'is_method' => true,
                    'opening_brace_line' => $i + 1,
                    'closing_brace_line' => $methodEndLine,
                ];
            }
        }

        // Fallback to regular findLine if method declaration pattern isn't found
        return $this->findLine($needle);
    }

    public function findChainedBlock(string $block): ?array
    {
        // Normalize the search block by removing extra whitespace
        $normalizedBlock = preg_replace('/\s+/', ' ', trim($block));

        // Split the block into individual method calls
        $methodCalls = array_map('trim', explode('->', $normalizedBlock));

        $lines = explode(PHP_EOL, $this->content);
        $contentLength = count($lines);

        for ($i = 0; $i < $contentLength; $i++) {
            $matchFound = true;
            $currentMethodIndex = 0;
            $startLine = $i;
            $endLine = $i;

            // Try to match all method calls in sequence
            while ($currentMethodIndex < count($methodCalls) && $endLine < $contentLength) {
                $currentLine = trim($lines[$endLine]);
                $normalizedLine = preg_replace('/\s+/', ' ', $currentLine);

                // Check if current line contains the current method call
                if (str_contains($normalizedLine, trim($methodCalls[$currentMethodIndex]))) {
                    $currentMethodIndex++;
                    $endLine++;
                } elseif (! empty($currentLine)) {
                    // If we find a non-empty line that doesn't match, break
                    $matchFound = false;

                    break;
                } else {
                    // Skip empty lines
                    $endLine++;
                }
            }

            if ($matchFound && $currentMethodIndex === count($methodCalls)) {
                // Calculate positions
                $startPos = 0;
                for ($j = 0; $j < $startLine; $j++) {
                    $startPos += strlen($lines[$j]) + strlen(PHP_EOL);
                }

                $endPos = $startPos;
                for ($j = $startLine; $j < $endLine; $j++) {
                    $endPos += strlen($lines[$j]) + strlen(PHP_EOL);
                }

                $indentation = preg_replace('/\S.*/', '', $lines[$startLine]);

                return [
                    'start' => $startPos,
                    'end' => $endPos,
                    'indentation' => $indentation,
                    'is_block' => true,
                ];
            }
        }

        return null;
    }

    public function containsChainedBlock(string $block): bool
    {
        return $this->findChainedBlock($block) !== null;
    }

    public function appendBlock(string $needle, string $contentToAppend, bool $afterBlock = false): static
    {
        if (! $this->contains($needle)) {
            return $this;
        }

        // Use findMethodDeclaration for better method handling
        $lineInfo = $this->findMethodDeclaration($needle);

        if (! $lineInfo) {
            return $this;
        }

        if ($afterBlock && isset($lineInfo['is_method']) && $lineInfo['is_method']) {
            // For method declarations, get the lines of content
            $lines = explode(PHP_EOL, $this->content);

            // Calculate proper indentation
            $methodIndent = $lineInfo['indentation'];
            $contentIndent = $methodIndent . str_repeat(' ', 4); // One level deeper than method

            // Format the content to append
            $contentLines = explode(PHP_EOL, trim($contentToAppend));
            $formattedContent = '';
            foreach ($contentLines as $index => $line) {
                $trimmedLine = trim($line);
                if (empty($trimmedLine)) {
                    continue;
                }

                $formattedContent .= ($index > 0 ? PHP_EOL . $methodIndent : '') . $contentIndent . $trimmedLine;
            }

            // Add new line if flag is set
            if ($this->addNewLine) {
                $formattedContent = $formattedContent . PHP_EOL;
                $this->addNewLine = false;
            }

            // Find position after opening brace
            $insertPos = 0;
            for ($i = 0; $i <= $lineInfo['opening_brace_line']; $i++) {
                $insertPos += strlen($lines[$i]) + strlen(PHP_EOL);
            }

            // Insert the formatted content
            $this->content = substr_replace(
                $this->content,
                $formattedContent . PHP_EOL . "\n",
                $insertPos,
                0
            );
        } else {
            // Original append logic
            $newContent = $lineInfo['indentation'] . $this->getIndentation() . trim($contentToAppend);
            if ($this->addNewLine) {
                $newContent = PHP_EOL . $newContent;
                $this->addNewLine = false;
            }

            $this->content = substr_replace(
                $this->content,
                $newContent,
                $lineInfo['end'],
                0
            );
        }

        return $this;
    }

    public function deleteLine(string $needle): static
    {
        if ($lineInfo = $this->findLine($needle)) {
            $this->content = substr_replace(
                $this->content,
                '',
                $lineInfo['start'],
                $lineInfo['end'] - $lineInfo['start']
            );
        }

        return $this;
    }
}
