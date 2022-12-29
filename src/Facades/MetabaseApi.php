<?php


namespace Ehsan9\MetabaseLaravel\Facades;

use Illuminate\Support\Facades\Facade;

class MetabaseApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'MetabaseApi';
    }

    public static function getQuestion(string $questionId, string $exportFormat = 'json', $parameters = NULL)
    {
        if(app()->has(\Ehsan9\MetabaseLaravel\MetabaseApi::class)) {
            return app()->get(\Ehsan9\MetabaseLaravel\MetabaseApi::class)->getQuestion($questionId, $exportFormat, $parameters);
        }

        $metabase = new \Ehsan9\MetabaseLaravel\MetabaseApi(
            config('metabase-api.url'), config('metabase-api.username'), config('metabase-api.password')
        );

        return $metabase->getQuestion($questionId, $exportFormat, $parameters);
    }
}
