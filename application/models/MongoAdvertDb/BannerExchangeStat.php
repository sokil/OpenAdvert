<?php 

namespace MongoAdvertDb;

class BannerExchangeStat extends \Sokil\Mongo\Collection
{
    public function getDocumentClassName(array $documentData = null) {
        return '\MongoAdvertDb\BannerExchangeStat\Record';
    }
}