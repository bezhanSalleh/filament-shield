<?php

/** @noinspection PhpSuspiciousNameCombinationInspection */

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield;

use Illuminate\Support\Traits\Conditionable;
use RuntimeException;

class Stringer
{
    use Conditionable;

    protected string $content;

    protected string $filePath;

    protected int $baseIndentLevel = 0; // Track the base indentation level

    protected int $currentIndentLevel = 0; // Track the current indentation level

    protected bool $addNewLine = false; // Track whether to add a new line

    public function __construct(string $filePath)
    {
        $this->filePath = static::normalizePath($filePath);
        $content = file_get_contents($this->filePath);

        if ($content === false) {
            throw new RuntimeException("Could not read file: {$this->filePath}");
        }

        // Normalize line endings to \n for cross-platform compatibility
        $this->content = str_replace(["\r\n", "\r"], "\n", $content);
    }

    public static function for(string $filePath): static
    {
        // Normalize file path for cross-OS compatibility
        $filePath = static::normalizePath($filePath);

        return app(static::class, ['filePath' => $filePath]);
    }

    /**
     * Normalize file path for cross-OS compatibility
     */
    protected static function normalizePath(string $path): string
    {
        // First, normalize directory separators to the current OS
        $normalizedPath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);

        // Try to resolve the path using realpath for existing files
        $realPath = realpath($normalizedPath);
        if ($realPath !== false) {
            return $realPath;
        }

        // If realpath failed (file doesn't exist yet), manually clean the path
        // Remove duplicate separators
        $normalizedPath = preg_replace('/[\\\\\/]+/', DIRECTORY_SEPARATOR, $normalizedPath);

        // Handle current directory references (./)
        $normalizedPath = str_replace(DIRECTORY_SEPARATOR . '.' . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $normalizedPath);

        // Handle parent directory references (../) by splitting and processing parts
        $parts = explode(DIRECTORY_SEPARATOR, $normalizedPath);
        $normalizedParts = [];

        foreach ($parts as $part) {
            if ($part === '..') {
                // Remove the last directory from the stack if it's not empty and not '..'
                if ($normalizedParts !== [] && end($normalizedParts) !== '..') {
                    array_pop($normalizedParts);
                } else {
                    $normalizedParts[] = $part;
                }
            } elseif ($part !== '.' && $part !== '') {
                $normalizedParts[] = $part;
            }
        }

        $normalizedPath = implode(DIRECTORY_SEPARATOR, $normalizedParts);

        // Preserve absolute path indicators for different OS
        if (PHP_OS_FAMILY === 'Windows') {
            // On Windows, preserve drive letter (e.g., C:)
            if (preg_match('/^[a-zA-Z]:/', $path) && in_array(preg_match('/^[a-zA-Z]:/', $normalizedPath), [0, false], true)) {
                $driveLetter = substr($path, 0, 2);
                $normalizedPath = $driveLetter . DIRECTORY_SEPARATOR . ltrim($normalizedPath, DIRECTORY_SEPARATOR);
            }
        } elseif (str_starts_with($path, '/') && ! str_starts_with($normalizedPath, DIRECTORY_SEPARATOR)) {
            // On Unix-like systems, preserve leading slash for absolute paths
            $normalizedPath = DIRECTORY_SEPARATOR . $normalizedPath;
        }

        return $normalizedPath;
    }

    protected function findLine(string $needle): ?array
    {
        // Search for the needle and return the line and its indentation
        $startPos = strpos($this->content, $needle);
        if ($startPos === false) {
            return null; // Not found
        }

        // Get the start of the line and calculate indentation
        $lineStartPos = strrpos(substr($this->content, 0, $startPos), "\n") ?: 0;
        $lineEndPos = strpos($this->content, "\n", $startPos) ?: strlen($this->content);

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
                ? "\n" . $indentation . trim($contentToPrepend)
                : $indentation . trim($contentToPrepend);
            $this->addNewLine = false;
            // Reset flag
            // Insert the formatted replacement before the opening parenthesis
            $this->content = substr_replace($this->content, $formattedReplacement, $openingParenPos, 0);
        } elseif (($lineInfo = $this->findLine($needle)) !== null && ($lineInfo = $this->findLine($needle)) !== []) {
            // Normal prepend logic
            // Prepend the content with proper indentation
            $newContent = $lineInfo['indentation'] . $this->getIndentation() . trim($contentToPrepend);
            if ($this->addNewLine) {
                $newContent = "\n" . $newContent;
                $this->addNewLine = false; // Reset the flag
            }
            $this->content = substr_replace(
                $this->content,
                $newContent,
                $lineInfo['start'],
                0
            );
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
                ? $indentation . trim($contentToAppend) . "\n"
                : $indentation . trim($contentToAppend);
            $this->addNewLine = false;
            // Reset flag
            // If the closing parenthesis has a semicolon, move it to a new line with indentation
            if ($this->content[$closingParenPos + 1] === ';') {
                $this->content = substr_replace(
                    $this->content,
                    "\n" . $indentation . ';',
                    $closingParenPos + 1,
                    0
                );
                $closingParenPos += strlen("\n" . $indentation . ';'); // Adjust closing position
            }
            // Insert the formatted replacement after the closing parenthesis
            $this->content = substr_replace($this->content, $formattedReplacement, $closingParenPos + 1, 0);
        } elseif (($lineInfo = $this->findLine($needle)) !== null && ($lineInfo = $this->findLine($needle)) !== []) {
            // Normal append logic
            // Append the content with proper indentation
            $newContent = $lineInfo['indentation'] . $this->getIndentation() . trim($contentToAppend);
            if ($this->addNewLine) {
                $newContent .= "\n";
                $this->addNewLine = false; // Reset the flag
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
        if (($lineInfo = $this->findLine($needle)) !== null && ($lineInfo = $this->findLine($needle)) !== []) {
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
        }

        // Perform an exact search
        return str_contains($this->content, $needle);
    }

    public function save(): bool
    {
        // Convert line endings to platform-specific format when saving
        $contentToSave = $this->content;

        // On Windows, convert \n to \r\n for proper line endings
        if (PHP_OS_FAMILY === 'Windows') {
            $contentToSave = str_replace("\n", "\r\n", $contentToSave);
        }

        return (bool) file_put_contents($this->filePath, $contentToSave);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function prependBeforeLast(string $needle, string $replacement): static
    {
        $lastPos = strrpos($this->content, $needle);

        if ($lastPos !== false) {
            $lineStartPos = strrpos(substr($this->content, 0, $lastPos), "\n") ?: 0;
            $line = substr($this->content, $lineStartPos, $lastPos - $lineStartPos);

            preg_match('/^\s*/', $line, $matches);
            $originalIndentation = $matches[0] ?? '';

            $formattedReplacement = $this->getIndentation() . trim($replacement);
            if ($this->addNewLine) {
                $formattedReplacement = "\n" . $formattedReplacement . "\n";
            }

            $this->addNewLine = false;

            $this->content = substr_replace($this->content, $originalIndentation . $formattedReplacement, $lineStartPos, 0);
        }

        return $this;
    }

    protected function findMethodDeclaration(string $needle): ?array
    {
        $lines = explode("\n", $this->content);
        $normalizedNeedle = preg_replace('/\s+/', ' ', trim($needle));
        $counter = count($lines);

        for ($i = 0; $i < $counter; $i++) {
            $currentLine = trim($lines[$i]);
            $nextLine = isset($lines[$i + 1]) ? trim($lines[$i + 1]) : '';

            // Check if current line contains the method declaration
            // and next line contains the opening brace
            if (str_contains((string) preg_replace('/\s+/', ' ', $currentLine), (string) $normalizedNeedle)
                && str_contains($nextLine, '{')) {

                $startPos = 0;
                for ($j = 0; $j < $i; $j++) {
                    $startPos += strlen($lines[$j]) + 1; // +1 for \n
                }

                $endPos = $startPos + strlen($lines[$i]) + 1 + strlen($lines[$i + 1]);
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
                    $methodEndPos += strlen($lines[$j]) + 1; // +1 for \n
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
        $methodCalls = array_map('trim', explode('->', (string) $normalizedBlock));

        $lines = explode("\n", $this->content);
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
                if (str_contains((string) $normalizedLine, trim($methodCalls[$currentMethodIndex]))) {
                    $currentMethodIndex++;
                    $endLine++;
                } elseif ($currentLine !== '' && $currentLine !== '0') {
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
                    $startPos += strlen($lines[$j]) + 1; // +1 for \n
                }

                $endPos = $startPos;
                for ($j = $startLine; $j < $endLine; $j++) {
                    $endPos += strlen($lines[$j]) + 1; // +1 for \n
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

        if ($lineInfo === null || $lineInfo === []) {
            return $this;
        }

        if ($afterBlock && isset($lineInfo['is_method']) && $lineInfo['is_method']) {
            // For method declarations, get the lines of content
            $lines = explode("\n", $this->content);

            // Calculate proper indentation
            $methodIndent = $lineInfo['indentation'];
            $contentIndent = $methodIndent . str_repeat(' ', 4); // One level deeper than method

            // Format the content to append
            $contentLines = explode("\n", trim($contentToAppend));
            $formattedContent = '';
            foreach ($contentLines as $index => $line) {
                $trimmedLine = trim($line);
                if ($trimmedLine === '') {
                    continue;
                }
                if ($trimmedLine === '0') {
                    continue;
                }

                $formattedContent .= ($index > 0 ? "\n" . $methodIndent : '') . $contentIndent . $trimmedLine;
            }

            // Add new line if flag is set
            if ($this->addNewLine) {
                $formattedContent .= "\n";
                $this->addNewLine = false;
            }

            // Find position after opening brace
            $insertPos = 0;
            for ($i = 0; $i <= $lineInfo['opening_brace_line']; $i++) {
                $insertPos += strlen($lines[$i]) + 1; // +1 for \n
            }

            // Insert the formatted content
            $this->content = substr_replace(
                $this->content,
                $formattedContent . "\n\n",
                $insertPos,
                0
            );
        } else {
            // Original append logic
            $newContent = $lineInfo['indentation'] . $this->getIndentation() . trim($contentToAppend);
            if ($this->addNewLine) {
                $newContent = "\n" . $newContent;
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
        if (($lineInfo = $this->findLine($needle)) !== null && ($lineInfo = $this->findLine($needle)) !== []) {
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
