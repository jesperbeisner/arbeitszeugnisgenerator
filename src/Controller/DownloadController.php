<?php

declare(strict_types=1);

namespace Jesperbeisner\Arbeitszeugnisgenerator\Controller;

use DateTime;
use Exception;
use Jesperbeisner\Arbeitszeugnisgenerator\Interface\ControllerInterface;
use Jesperbeisner\Arbeitszeugnisgenerator\Interface\ResponseInterface;
use Jesperbeisner\Arbeitszeugnisgenerator\Stdlib\Request;
use Jesperbeisner\Arbeitszeugnisgenerator\Stdlib\FileResponse;
use Jesperbeisner\Arbeitszeugnisgenerator\Stdlib\RedirectResponse;
use PhpOffice\PhpWord\Exception\Exception as PhpOfficeException;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Language;
use RuntimeException;

final readonly class DownloadController implements ControllerInterface
{
    /**
     * @param list<array{subject: string, name: string, required: bool, texts: array{1: string, 2: string, 3: string, 4: string}}> $textsArray
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
            $firstName = $request->post['firstName'] ?? throw new RuntimeException('No first name found');
            $firstName = trim($firstName);

            if ($firstName === '') {
                throw new RuntimeException('First name not long enough');
            }

            $lastName = $request->post['lastName'] ?? throw new RuntimeException('No last name found');
            $lastName = trim($lastName);

            if ($lastName === '') {
                throw new RuntimeException('Last name not long enough');
            }

            $gender = $request->post['gender'] ?? throw new RuntimeException('No gender found');

            if (!in_array($gender, ['f', 'm'], true)) {
                throw new RuntimeException('No valid gender');
            }

            $leaveDate = $request->post['leaveDate'] ?? throw new RuntimeException('No leave date found');

            try {
                $leaveDate = new DateTime($leaveDate);
            } catch (Exception) {
                throw new RuntimeException('No valid leave date');
            }
        } catch (RuntimeException) {
            return new RedirectResponse('/?error=validation');
        }

        $fileName = sys_get_temp_dir() . '/Arbeitszeugnis-' . date('Y-m-d-H-i-s') . '.docx';

        $phpWord = new PhpWord();
        $phpWord->getSettings()->setThemeFontLang(new Language(Language::DE_DE));

        $section = $phpWord->addSection();
        $section->addText(sprintf('%s %s %s,', $gender === 'f' ? 'Frau' : 'Herr', $firstName, $lastName));
        $section->addTextBreak();

        $text = '';
        $paragraphs = 0;
        foreach ($this->textsArray as $textArray) {
            if ($textArray['name'] === 'schlussformulierungen') {
                continue;
            }

            if (null === $grade = $request->post[$textArray['name']] ?? null) {
                if ($textArray['required'] === true) {
                    throw new RuntimeException(sprintf('Field "%s" is required.', $textArray['subject']));
                } else {
                    continue;
                }
            }

            if (!array_key_exists((int) $grade, $textArray['texts'])) {
                if ($textArray['required'] === true) {
                    throw new RuntimeException(sprintf('Grade for field "%s" is not valid.', $textArray['subject']));
                } else {
                    continue;
                }
            }

            $text .= $this->replaceTextPlaceholder($textArray['texts'][(int) $grade], $gender, $lastName, $leaveDate) . ' ';
            $paragraphs++;

            if ($paragraphs === 3) {
                $section->addText($text);
                $section->addTextBreak();

                $text = '';
                $paragraphs = 0;
            }
        }

        $extraText = false;
        if ($text !== '') {
            $extraText = true;
            $section->addText($text);
        }

        // Add "schlussformulierungen" as an own paragraph in the end
        if (null === $grade = $request->post['schlussformulierungen'] ?? null) {
            throw new RuntimeException('Field "schlussformulierungen" is required.');
        }

        foreach ($this->textsArray as $textArray) {
            if ($textArray['name'] === 'schlussformulierungen') {
                if (!array_key_exists((int) $grade, $textArray['texts'])) {
                    throw new RuntimeException('Grade for field "schlussformulierungen" is not valid.');
                }

                if ($extraText === true) {
                    $section->addTextBreak();
                }

                $section->addText($this->replaceTextPlaceholder($textArray['texts'][(int) $grade], $gender, $lastName, $leaveDate));
            }
        }

        try {
            IOFactory::createWriter($phpWord)->save($fileName);
        } catch (PhpOfficeException) {
            return new RedirectResponse('/?error=server');
        }

        return new FileResponse($fileName);
    }

    private function replaceTextPlaceholder(string $text, string $gender, string $lastName, DateTime $leaveDate): string
    {
        $text = str_replace('%Frau/Herr%', $gender === 'f' ? 'Frau' : 'Herr', $text);
        $text = str_replace('%Frau/Herrn%', $gender === 'f' ? 'Frau' : 'Herrn', $text);
        $text = str_replace('%Nachname%', $lastName, $text);
        $text = str_replace('%Sie/Er%', $gender === 'f' ? 'Sie' : 'Er', $text);
        $text = str_replace('%sie/er%', $gender === 'f' ? 'sie' : 'er', $text);
        $text = str_replace('%ihrer/seiner%', $gender === 'f' ? 'ihrer' : 'seiner', $text);
        $text = str_replace('%ihre/seine%', $gender === 'f' ? 'ihre' : 'seine', $text);
        $text = str_replace('%Ihre/Seine%', $gender === 'f' ? 'Ihre' : 'Seine', $text);
        $text = str_replace('%ihr/sein%', $gender === 'f' ? 'ihr' : 'sein', $text);
        $text = str_replace('%ihr/ihm%', $gender === 'f' ? 'ihr' : 'ihm', $text);
        $text = str_replace('%ihren/seinen%', $gender === 'f' ? 'ihren' : 'seinen', $text);
        $text = str_replace('%eine/ein%', $gender === 'f' ? 'eine' : 'ein', $text);
        $text = str_replace('%motivierte/motivierter%', $gender === 'f' ? 'motivierte' : 'motivierter', $text);
        $text = str_replace('%Mitarbeiterin/Mitarbeiter%', $gender === 'f' ? 'Mitarbeiterin' : 'Mitarbeiter', $text);
        $text = str_replace('%belastbare/belastbarer%', $gender === 'f' ? 'belastbare' : 'belastbarer', $text);
        $text = str_replace('%zuverlässige/zuverlässiger%', $gender === 'f' ? 'zuverlässige' : 'zuverlässiger', $text);
        $text = str_replace('%Austrittsdatum%', $leaveDate->format('d.m.Y'), $text);

        return $text;
    }
}
