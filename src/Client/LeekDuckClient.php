<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Client;

use Endroid\Asset\Factory\AssetFactory;
use Endroid\Calendar\Model\Calendar;
use Endroid\Calendar\Model\CalendarItem;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Uid\Uuid;

final readonly class LeekDuckClient
{
    public function __construct(
        private AssetFactory $assetFactory,
    ) {
    }

    public function getCalendar(): Calendar
    {
        $skipCategories = ['GO Battle League', 'Season', 'Research Breakthrough', 'Update', 'PokéStop Showcase'];
        $skipTitles = ['Amazon'];

        $asset = $this->assetFactory->create(null, [
            'url' => 'https://leekduck.com/events/',
            'cache_key' => 'leekduck-event-overview',
            'cache_expires_after' => 8640,
        ]);

        $crawler = new Crawler($asset->getData());

        $eventUrls = $crawler->filter('.event-item-link')->each(function (Crawler $node) use ($skipCategories) {
            $category = $node->filter('p')->innerText();
            foreach ($skipCategories as $skipCategory) {
                if (strtolower($skipCategory) === strtolower($category)) {
                    return null;
                }
            }

            return 'https://leekduck.com'.$node->attr('href');
        });

        $eventUrls = array_unique(array_filter($eventUrls));

        $asset = $this->assetFactory->create(null, [
            'urls' => array_combine($eventUrls, $eventUrls),
            'cache_key' => 'leekduck-event-details',
            'cache_expires_after' => 8640,
        ]);

        $assetData = unserialize($asset->getData());

        $events = [];
        foreach ($eventUrls as $assetUrl) {
            $crawler = new Crawler($assetData[$assetUrl]);
            $title = $crawler->filter('.page-title')->innerText();
            foreach ($skipTitles as $skipTitle) {
                if (str_contains($title, $skipTitle)) {
                    continue 2;
                }
            }
            try {
                $dateStart = $crawler->filter('#event-date-start')->innerText();
                $timeStart = $crawler->filter('#event-time-start')->innerText();
                $dateEnd = $crawler->filter('#event-date-end')->innerText();
                $timeEnd = $crawler->filter('#event-time-end')->innerText();
                $dateTimeStart = $this->createDateTime($dateStart, $timeStart);
                $dateTimeEnd = $this->createDateTime($dateEnd, $timeEnd);

                if ($dateTimeStart->diff($dateTimeEnd)->days < 25) {
                    $events[] = new CalendarItem((string) Uuid::v6(), $title, $assetUrl, $dateTimeStart, $dateTimeEnd);
                }
            } catch (\Throwable) {
            }
        }

        return new Calendar('Pokemon Go', $events);
    }

    private function createDateTime(string $dateString, string $timeString): \DateTimeImmutable
    {
        $extractedDate = trim($dateString, ',');
        $timeString = str_replace('\u{A0}', '', $timeString);
        preg_match('#([0-9]+:[0-9]+).*(AM|PM)#', $timeString, $extractedTime);

        if (!isset($extractedTime[1], $extractedTime[2])) {
            throw new \RuntimeException('Could not extract time');
        }

        /** @var \DateTimeImmutable $dateTime */
        $dateTime = \DateTimeImmutable::createFromFormat(
            'l, F j, Y H:i A',
            $extractedDate.' '.$extractedTime[1].' '.$extractedTime[2],
            new \DateTimeZone('Europe/Amsterdam')
        );

        return $dateTime;
    }
}
