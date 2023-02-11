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
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Language;
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
            $firstName = $request->post['firstName'] ?? throw new RuntimeException('No first name found');

            if (strlen($firstName) < 3) {
                throw new RuntimeException('First name not long enough');
            }

            $lastName = $request->post['lastName'] ?? throw new RuntimeException('No last name found');

            if (strlen($lastName) < 3) {
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

        $fileName = sys_get_temp_dir() . '/Arbeitszeugnis-' . date('Y-m-d_H-i-s') . '.docx';

        $phpWord = new PhpWord();
        $phpWord->getSettings()->setThemeFontLang(new Language(Language::DE_DE));

        $section = $phpWord->addSection();
        $section->addText(sprintf('%s %s %s,', $gender === 'f' ? 'Frau' : 'Herr', $firstName, $lastName));
        $section->addTextBreak();

        $text = '';
        foreach ($this->textsArray as $subject => $texts) {
            $subject = strtolower(str_replace(' ', '_', $subject));
            $grade = $request->post[$subject] ?? null;

            if ($grade === null) {
                continue;
            }

            if (!array_key_exists((int) $grade, $texts)) {
                continue;
            }

            $text .= $this->replaceTextPlaceholder($texts[(int) $grade], $gender, $lastName, $leaveDate) . ' ';
        }

        $section->addText($text);

        IOFactory::createWriter($phpWord)->save($fileName);

        return new FileResponse($fileName);
    }

    private function replaceTextPlaceholder(string $text, string $gender, string $lastName, \DateTime $leaveDate): string
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
        $text = str_replace('%eine/ein%', $gender === 'f' ? 'eine' : 'ein', $text);
        $text = str_replace('%motivierte/motivierter%', $gender === 'f' ? 'motivierte' : 'motivierter', $text);
        $text = str_replace('%Mitarbeiterin/Mitarbeiter%', $gender === 'f' ? 'Mitarbeiterin' : 'Mitarbeiter', $text);
        $text = str_replace('%belastbare/belastbarer%', $gender === 'f' ? 'belastbare' : 'belastbarer', $text);
        $text = str_replace('%zuverlässige/zuverlässiger%', $gender === 'f' ? 'zuverlässige' : 'zuverlässiger', $text);
        $text = str_replace('%Austrittsdatum%', $leaveDate->format('d.m.Y'), $text);

        return $text;
    }
}
