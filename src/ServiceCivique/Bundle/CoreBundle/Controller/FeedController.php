<?php

namespace ServiceCivique\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FeedController extends Controller
{
    /**
     * Generate the media feed
     *
     * @return Response XML Feed
     */
    public function mediaAction()
    {

        $item = $this->getDoctrine()->getRepository('ServiceCiviqueCoreBundle:Media')->findAll();
        $feed = $this->get('eko_feed.feed.manager')->get('media');
        $feed->addFromArray($item);

        return new Response($feed->render('rss')); // or 'atom'
    }

    /**
     * Generate the actualite feed
     *
     * @return Response XML Feed
     */
    public function actualiteAction()
    {

        $item = $this->getDoctrine()->getRepository('ServiceCiviqueCoreBundle:Actu')->findAll();
        $feed = $this->get('eko_feed.feed.manager')->get('actualite');
        $feed->addFromArray($item);

        return new Response($feed->render('rss')); // or 'atom'
    }

    /**
     * Generate the video feed
     *
     * @return Response XML Feed
     */
    public function videoAction()
    {

        $item = $this->getDoctrine()->getRepository('ServiceCiviqueCoreBundle:Video')->findAll();
        $feed = $this->get('eko_feed.feed.manager')->get('video');
        $feed->addFromArray($item);

        return new Response($feed->render('rss')); // or 'atom'
    }

    /**
     * Generate the presse feed
     *
     * @return Response XML Feed
     */
    public function presseAction()
    {

        $item = $this->getDoctrine()->getRepository('ServiceCiviqueCoreBundle:Presse')->findAll();
        $feed = $this->get('eko_feed.feed.manager')->get('presse');
        $feed->addFromArray($item);

        return new Response($feed->render('rss')); // or 'atom'
    }

    /**
     * Generate the avantage feed
     *
     * @return Response XML Feed
     */
    public function avantageAction()
    {

        $item = $this->getDoctrine()->getRepository('ServiceCiviqueCoreBundle:Advantage')->findAll();
        $feed = $this->get('eko_feed.feed.manager')->get('avantage');
        $feed->addFromArray($item);

        return new Response($feed->render('rss')); // or 'atom'
    }

    /**
     * Generate the partenaire feed
     *
     * @return Response XML Feed
     */
    public function partenaireAction()
    {

        $item = $this->getDoctrine()->getRepository('ServiceCiviqueCoreBundle:Partner')->findAll();
        $feed = $this->get('eko_feed.feed.manager')->get('partenaire');
        $feed->addFromArray($item);

        return new Response($feed->render('rss')); // or 'atom'
    }
}
