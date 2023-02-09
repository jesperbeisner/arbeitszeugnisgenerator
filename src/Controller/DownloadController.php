<?php

declare(strict_types=1);

namespace Jesperbeisner\Arbeitszeugnisgenerator\Controller;

use Jesperbeisner\Arbeitszeugnisgenerator\Interface\ControllerInterface;
use Jesperbeisner\Arbeitszeugnisgenerator\Interface\ResponseInterface;
use Jesperbeisner\Arbeitszeugnisgenerator\Stdlib\Request;
use Jesperbeisner\Arbeitszeugnisgenerator\Stdlib\FileResponse;
use Jesperbeisner\Arbeitszeugnisgenerator\Stdlib\RedirectResponse;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use RuntimeException;

final readonly class DownloadController implements ControllerInterface
{
    /**
     * @param array<string, array<int, string>> $textsArray
     */
    public function __construct(
        private array $textsArray,
    ) {
    }

    public function execute(Request $request): ResponseInterface
    {
        if ($request->getMethod() !== 'POST') {
            return new RedirectResponse('/');
        }

        try {
            $name = $request->post['name'] ?? throw new RuntimeException('No name found');

            if (strlen($name) < 3) {
                throw new RuntimeException('Name not long enough');
            }

            $gender = $request->post['gender'] ?? throw new RuntimeException('No gender found');

            if (!in_array($gender, ['f', 'm'], true)) {
                throw new RuntimeException('No valid gender');
            }

            $salutation = $gender === 'f' ? 'Frau' : 'Herr';

            $fileName = sys_get_temp_dir() . '/Arbeitszeugnis-' . time() . '.docx';

            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            $section->addText(sprintf('%s %s,', $salutation, $name));
            $section->addTextBreak();

            foreach ($this->textsArray as $subject => $text) {
                $subject = str_replace(' ', '_', $subject);
                $grade = $request->post[strtolower($subject)] ?? throw new RuntimeException(sprintf('Subject "%s" not filled.', $subject));

                $section->addText($text[(int) $grade] ?? throw new RuntimeException('Error'));
                $section->addTextBreak();
            }

            IOFactory::createWriter($phpWord)->save($fileName);
        } catch (RuntimeException) {
            return new RedirectResponse('/');
        }

        return new FileResponse($fileName);
    }
}
