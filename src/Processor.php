<?php

namespace Leve\Uploader;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Str;

class Processor
{
    /**
     * @var array
     */
    protected array $breakpoints = [
        25,
        100,
        320,
        414,
        667,
        736,
        768,
        1024,
        1920
    ];

    protected $file;

    protected Image $factory;

    protected string $output;

    protected array $pipes = [];

    /**
     * @var string|null
     */
    protected ?string $directory = null;

    /**
     * @param $file
     * @param string $output
     */
    public function __construct($file, string $output = null)
    {
        $this->file = $file;
        $this->output = $output;
        $this->setDirectory("{$this->output}/{$this->getHash()}");
    }

    /**
     * @param $file
     * @param string|null $output
     * @param array $sizes
     * @return Processor
     */
    public static function make($file, string $output = null, array $sizes = []): Processor
    {
        $instance = new self($file, $output);
        $instance->breakpoints = $sizes;
        return $instance;
    }

    /**
     * Processaar imagem
     *
     * @return array
     */
    public function process(): array
    {
        foreach ($this->breakpoints as $size) {
            $upload = $this->pipe($size);

            if (!$upload) {
                unset($this->pipes[$size]);
            }
        }

        return $this->pipes;
    }

    /**
     * Reverter upload
     *
     * @param int $size
     * @return bool
     */
    public function pipe(int $size)
    {
        $image = Image::make($this->file);

        // caso o tamanho do ajuste seja maior que a original
        if ($size > $image->width()) {
            return false;
        }

        // redimenssionar imagem
        $make = $image->resize($size, null, fn ($h) => $h->aspectRatio());

        // nome do arquivo
        $filename = str_replace('-', '', "{$this->getDirectory()}/{$size}.webp");

        // processo
        $this->pipes[$size] = $filename;

        $encoded = $make->encode('webp', 100)->encoded;

        return Storage::disk('s3')->put($filename, $encoded, 'public');
    }

    /**
     * Determinar caminho
     *
     * @param string $dir
     * @return Processor
     */
    public function setDirectory(string $dir): Processor
    {
        $this->directory = $dir;

        return $this;
    }

    /**
     * Obter diretório gerado.
     *
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * Chave do processo
     *
     * @return string
     */
    public function getHash(): string
    {
        return Str::random(32);
    }

    /**
     * Reverter upload
     *
     * @return void
     */
    public function revert()
    {
        $fs = app('filesystem');

        if ($fs->exists($this->directory)) {
            $fs->deleteDirectory($this->directory);
        }
    }
}
