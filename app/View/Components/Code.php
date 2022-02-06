<?php

namespace App\View\Components;

use Illuminate\Support\Str;
use Torchlight\Blade\{BladeManager, CodeComponent};
use Torchlight\Torchlight;

class Code extends CodeComponent
{
    private bool $noCopy;

    private array $options;

    public function __construct(
        string $language = 'text',
        ?string $theme = null,
        ?string $contents = null,
        ?array $swap = null,
        array $postProcessors = [],
        ?string $torchlightId = null,
        bool $noCopy = false,
        array $options = [],
    ) {
        parent::__construct($language, $theme, $contents, $swap, $postProcessors, $torchlightId);

        $this->noCopy  = $noCopy;
        $this->options = $options;
    }

    private function makePreAttributes(): string
    {
        $attributes = [
            'data-nocopy="true"' => $this->noCopy,
        ];

        return collect($attributes)->filter()->keys()->implode(' ');
    }

    public function capture($contents)
    {
        $contents = $contents ?: $this->contents;
        $contents = Torchlight::processFileContents($contents) ?: $contents;

        if (Str::startsWith($contents, $this->trimFixDelimiter)) {
            $contents = Str::replaceFirst($this->trimFixDelimiter, '', $contents);
        }

        $this->block->code($contents);

        if ($this->options) {
            $options = json_encode($this->options);

            $this->block->code = "// torchlight! {$options}\n{$this->block->code}";
        }

        BladeManager::registerBlock($this->block);
    }

    public function render()
    {
        $html = parent::render();

        return "<pre {$this->makePreAttributes()}>{$html}</pre>";
    }
}
