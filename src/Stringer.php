<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield;

use Closure;
use Illuminate\Support\Traits\Conditionable;

class Stringer
{
    use Conditionable;

    protected string $content;

    protected string $filePath;

    protected int $baseIndentLevel = 0; // Track the base indentation level

    protected int $currentIndentLevel = 0; // Track the current indentation level

    protected bool $addNewLine = false; // Track whether to add a new line

    public static function for(string $filePath): self
    {
        return new self($filePath);
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

    public function prepend(string $needle, string $contentToPrepend): self
    {
        if ($lineInfo = $this->findLine($needle)) {
            // Prepend the content with proper indentation
            $newContent = $lineInfo['indentation'] . $this->getIndentation() . trim($contentToPrepend);
            if ($this->addNewLine) {
                $newContent .= PHP_EOL;
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

    public function append(string $needle, string $contentToAppend): self
    {
        if ($lineInfo = $this->findLine($needle)) {
            // Append the content with proper indentation
            $newContent = $lineInfo['indentation'] . $this->getIndentation() . trim($contentToAppend);
            if ($this->addNewLine) {
                $newContent = PHP_EOL . $newContent;
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

    public function replace(string $needle, string $replacement): self
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

    public function newLine(): self
    {
        $this->addNewLine = true; // Set the flag to add a new line

        return $this;
    }

    public function indent(int $level): self
    {
        $this->currentIndentLevel += $level;

        return $this;
    }

    public function deindent(int $level): self
    {
        $this->currentIndentLevel = max(0, $this->currentIndentLevel - $level);

        return $this;
    }

    public function getIndentation(): string
    {
        return str_repeat(' ', $this->baseIndentLevel + $this->currentIndentLevel);
    }

    public function replaceFirst(string $needle, string $replacement): self
    {
        if ($lineInfo = $this->findLine($needle)) {
            $indentedReplacement = $lineInfo['indentation'] . trim($replacement);

            // Replace the first occurrence line using `start` and `end` positions
            $this->content = substr_replace(
                $this->content,
                $indentedReplacement,
                $lineInfo['start'],
                $lineInfo['end'] - $lineInfo['start']
            );
        }

        return $this;
    }

    public function replaceLast(string $needle, string $replacement): self
    {
        $lastPos = strrpos($this->content, $needle);
        if ($lastPos !== false) {
            // Use findLine based on the last occurrence's position
            $lineInfo = $this->findLine($needle);

            if ($lineInfo) {
                $indentedReplacement = $lineInfo['indentation'] . trim($replacement);

                // Replace the last occurrence with proper indentation
                $this->content = substr_replace(
                    $this->content,
                    $indentedReplacement,
                    $lineInfo['start'],
                    $lineInfo['end'] - $lineInfo['start']
                );
            }
        }

        return $this;
    }

    public function contains(string $needle): bool
    {
        return strpos($this->content, $needle) !== false;
    }

    public function save(): bool
    {
        return (bool) file_put_contents($this->filePath, $this->content);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    // New chaining method that allows for indentations while performing operations
    public function execute(Closure $closure): self
    {
        $closure($this); // Call the closure, allowing operations to be performed

        return $this;
    }

    public function appendAfterLast(string $needle, string $replacement): self
    {
        $lastPos = strrpos($this->content, $needle);

        if ($lastPos !== false) {
            $insertPos = $lastPos + strlen($needle);

            $nextLinePos = strpos($this->content, PHP_EOL, $insertPos) ?: strlen($this->content);
            $followingLine = substr($this->content, $insertPos, $nextLinePos - $insertPos);
            preg_match('/^\s*/', $followingLine, $matches);
            $followingIndentation = $matches[0] ?? '';

            $formattedReplacement = trim($replacement);
            if ($this->addNewLine) {
                $formattedReplacement = PHP_EOL . $formattedReplacement . PHP_EOL;
            }

            $this->addNewLine = false;

            $this->content = substr_replace($this->content, $followingIndentation . $formattedReplacement, $insertPos, 0);
        }

        return $this;
    }

    public function prependBeforeLast(string $needle, string $replacement): self
    {
        $lastPos = strrpos($this->content, $needle);

        if ($lastPos !== false) {
            $lineStartPos = strrpos(substr($this->content, 0, $lastPos), PHP_EOL) ?: 0;
            $line = substr($this->content, $lineStartPos, $lastPos - $lineStartPos);

            preg_match('/^\s*/', $line, $matches);
            $originalIndentation = $matches[0] ?? '';

            $formattedReplacement = trim($replacement);
            if ($this->addNewLine) {
                $formattedReplacement = PHP_EOL . $formattedReplacement . PHP_EOL;
            }

            $this->addNewLine = false;

            $this->content = substr_replace($this->content, $originalIndentation . $formattedReplacement, $lineStartPos, 0);
        }

        return $this;
    }
}
