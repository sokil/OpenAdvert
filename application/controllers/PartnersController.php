<?php
class PartnersController extends Controller
{

    public function actionPartnersList($query)
    {
        /* @var $partnerSearch \MongoAdvertDb\Partners\QueryExpression */
        $partnerSearch = Yii::app()->mongo
            ->getCollection('partners')
            ->find()
            ->active();

        if (strlen($query) >= 3) {
            $partnerSearch->whereOr(
                array(
                    $partnerSearch->expression()->whereLike('ref', '^' . $query),
                    $partnerSearch->expression()->whereLike('name', $query)
                )
            );
        } else {
            $partnerSearch->whereLike('ref', '^' . $query);
        }
        
        echo json_encode(array_values(array_map(function($partner) {
            return array(
                'id'    => (string) $partner->getId(),
                'ref'   => $partner->getRef(),
                'name'  => $partner->getName(),
            );
        }, $partnerSearch->findAll())));
    }
}