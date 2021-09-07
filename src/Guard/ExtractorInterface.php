<?php


namespace Evrinoma\SecurityBundle\Guard;


use Symfony\Component\HttpFoundation\Request;

interface ExtractorInterface
{
//region SECTION: Public
    /**
     * @param Request $request
     */
    public function extract(Request $request):void;
//endregion Public
}