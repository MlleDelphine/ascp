<?php

namespace ServiceCivique\Bundle\CoreBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use ServiceCivique\Bundle\CoreBundle\Entity\ForbiddenKeyword;

class ContentCheckerService {

    protected $doctrine;
    protected $em;
    protected $forbiddenKeywordRepository;

    public function __construct(
    Registry $doctrine, \Doctrine\ORM\EntityRepository $forbiddenWordRepository
    ) {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getManager();
        $this->forbiddenKeywordRepository = $forbiddenWordRepository;
    }

    public function checkContent($content, $add_hit_to_keyword = true) {

        $forbidden_keywords = $this->forbiddenKeywordRepository->findAllAndReturnsNames();
        array_walk($forbidden_keywords, function(&$value, $key) {
            $value = $value['name'];
        });

        $matchFound = preg_match_all("/\b(" . implode($forbidden_keywords, "|") . ")\b/i", $content, $matches);

        if ($matchFound) {
            if($add_hit_to_keyword) {
                foreach ($matches[0] as $keyword_name){
                    $this->addHitToKeyword($keyword_name);
                }
            }
        }
        
        return $matchFound;
    }
    
    protected function addHitToKeyword($keyword_name)
    {
        /* @var $keyword \ServiceCivique\Bundle\CoreBundle\Entity\ForbiddenKeyword */
        $keyword = $this->forbiddenKeywordRepository->findOneByName($keyword_name);
        if($keyword) {
            $keyword->addOneHit();
            $this->em->flush();
        }
    }

}
