<?php
declare(strict_types=1);

namespace Action;

use Auth;
use Database\MysqlParam;
use Database\WhereClause\ComparisonCondition;
use Entity\Offer;
use HttpResponse;

class HomeAction extends SimplePageAction
{
    protected static $pathParts = [];

    public function execute () : HttpResponse
    {
        $user = Auth::getInstance()->getLoggedInUser();

        if ($user !== null) {
            return $this->renderView('home.php', [
                'currentOffer' => Offer::fetchOneByCriteria(
                    ComparisonCondition::equals('id', MysqlParam::integer($user->currentOfferId))
                )
            ]);
        }

        return $this->renderView('home.php', [
            'offers' => Offer::fetchAll()
        ]);
    }
}