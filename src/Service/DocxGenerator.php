<?php

namespace App\Service;

use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;
use function sys_get_temp_dir;
use function tempnam;
use const ENT_QUOTES;
use const ENT_SUBSTITUTE;

final class DocxGenerator
{

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function generate(string $templatePath, array $vars): string
    {
        $tp = new TemplateProcessor($templatePath);

        foreach ($vars as $key => $value) {
            $value = htmlspecialchars(
                (string)$value,
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );

            $tp->setValue((string)$key, $value);
        }
        $out = tempnam(sys_get_temp_dir(), 'docx_') . '.docx';
        $tp->saveAs($out);

        return $out;
    }
}
