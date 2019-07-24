<?php
declare(strict_types=1);

namespace Action;

use Database\MysqlParam;
use Database\WhereClause\ComparisonCondition;
use Database\WhereClause\OrCombination;
use Entity\Album;
use Entity\Artist;
use Entity\Genre;
use Entity\Track;
use HttpResponse;

class SearchAction extends SimplePageAction
{
    protected static $pathParts = ['search'];

    public function execute (): HttpResponse
    {
        if (!isset($_GET['q'])) {
            return $this->renderView('search.php');
        }

        $query = trim($_GET['q']);

        if (strlen($query) === 0) {
            return $this->renderView('search.php');
        }

        $queryParts = explode(' ', $query);

        $artists = Artist::fetchByCriteria($this->prepareComparisonsForColumn('title', $queryParts));
        $albums = Album::fetchByCriteria($this->prepareComparisonsForColumn('title', $queryParts));
        $genres = Genre::fetchByCriteria($this->prepareComparisonsForColumn('title', $queryParts));
        $tracks = Track::fetchByCriteria($this->prepareComparisonsForColumn('title', $queryParts));

        return $this->renderView('search.php', [
            'query' => $query,

            'artists' => $artists,
            'albums' => $albums,
            'genres' => $genres,
            'tracks' => $tracks,
        ]);
    }

    private function prepareComparisonsForColumn (string $column, array $parts): OrCombination
    {
        $conditions = [];

        foreach ($parts as $part) {
            $conditions[] = ComparisonCondition::like($column, MysqlParam::string("%$part%"));
        }

        return OrCombination::fromConditions($conditions);
    }
}