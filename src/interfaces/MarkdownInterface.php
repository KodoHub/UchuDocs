<?php

namespace Documentation\interfaces;

interface MarkdownInterface {
    public function parseFile(string $filePath): string;
    public function parseContent(string $content): string;
}