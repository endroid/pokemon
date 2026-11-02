<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Tests;

use Endroid\Asset\DataAsset;
use Endroid\Asset\Factory\Adapter\FactoryAdapterInterface;
use Endroid\Asset\Factory\AssetFactory;
use Endroid\Pokemon\Client\LeekDuckClient;
use Endroid\Pokemon\Client\PoGoApiClient;
use Endroid\Pokemon\Client\PvPokeClient;
use Endroid\Pokemon\Model\Type;
use Endroid\Pokemon\PokemonRepository;
use PHPUnit\Framework\TestCase;

final class ReadersTest extends TestCase
{
    public function testReleasedPokemonTypes(): void
    {
        $factory = $this->createAssetFactory([
            'pokemon-released-pokemon' => '{"1":{"id":1,"name":"Bulbasaur"}}',
            'pokemon-pokemon-stats' => '[{"pokemon_id":1,"pokemon_name":"Bulbasaur","form":"Normal","base_attack":118,"base_defense":111,"base_stamina":128}]',
            'pokemon-pokemon-types' => '[{"pokemon_id":1,"type":["Grass","Poison"]}]',
        ]);

        $pokemon = new PokemonRepository(new PoGoApiClient($factory), new PvPokeClient($factory))
            ->findAll()
            ->find('Bulbasaur', 'Normal');

        self::assertSame([Type::Grass, Type::Poison], $pokemon->types);
        self::assertSame(1, $pokemon->number);
    }

    public function testMissingReleasedPokemonTypesAreRejected(): void
    {
        $factory = $this->createAssetFactory([
            'pokemon-released-pokemon' => '{"1":{"id":1,"name":"Bulbasaur"}}',
            'pokemon-pokemon-stats' => '[{"pokemon_id":1,"pokemon_name":"Bulbasaur","form":"Normal","base_attack":118,"base_defense":111,"base_stamina":128}]',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Missing types for Pokemon "1"');

        new PokemonRepository(new PoGoApiClient($factory), new PvPokeClient($factory))->findAll();
    }

    public function testUnreleasedPokemonDoNotNeedTypes(): void
    {
        $factory = $this->createAssetFactory([
            'pokemon-pokemon-stats' => '[{"pokemon_id":1,"pokemon_name":"Bulbasaur","form":"Normal","base_attack":118,"base_defense":111,"base_stamina":128}]',
        ]);

        $collection = new PokemonRepository(new PoGoApiClient($factory), new PvPokeClient($factory))->findAll();

        self::assertSame([], iterator_to_array($collection));
    }

    public function testEmptyEventOverview(): void
    {
        $factory = $this->createAssetFactory([
            'leekduck-event-overview' => '<html><body></body></html>',
            'leekduck-event-details' => serialize([]),
        ]);

        self::assertSame([], new LeekDuckClient($factory)->getCalendar()->getCalendarItems());
    }

    public function testMissingEventDetailsAreRejected(): void
    {
        $factory = $this->createAssetFactory([
            'leekduck-event-overview' => '<a class="event-item-link" href="/events/test/"><p>Event</p></a>',
            'leekduck-event-details' => serialize([]),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Missing event data for "https://leekduck.com/events/test/"');

        new LeekDuckClient($factory)->getCalendar();
    }

    public function testEventDetailsAreRead(): void
    {
        $factory = $this->createAssetFactory([
            'leekduck-event-overview' => '<a class="event-item-link" href="/events/test/"><p>Event</p></a>',
            'leekduck-event-details' => serialize([
                'https://leekduck.com/events/test/' => <<<'HTML'
                    <h1 class="page-title">Test Event</h1>
                    <span id="event-date-start">Monday, November 2, 2026</span>
                    <span id="event-time-start">10:00 AM</span>
                    <span id="event-date-end">Monday, November 2, 2026</span>
                    <span id="event-time-end">11:00 AM</span>
                    HTML,
            ]),
        ]);

        $items = new LeekDuckClient($factory)->getCalendar()->getCalendarItems();

        self::assertCount(1, $items);
        self::assertSame('Test Event', $items[0]->getTitle());
        self::assertSame('2026-11-02T10:00:00+01:00', $items[0]->getDateStart()->format('c'));
    }

    /** @param array<string, string> $responses */
    private function createAssetFactory(array $responses): AssetFactory
    {
        $adapter = $this->createStub(FactoryAdapterInterface::class);
        $adapter->method('getAssetClassName')->willReturn(DataAsset::class);
        $adapter->method('isValidGuess')->willReturn(true);
        $adapter
            ->method('create')
            ->willReturnCallback(
                static fn(array $options): DataAsset => new DataAsset($responses[$options['cache_key']] ?? '[]'),
            );

        $factory = new AssetFactory();
        $factory->addFactory($adapter);

        return $factory;
    }
}
