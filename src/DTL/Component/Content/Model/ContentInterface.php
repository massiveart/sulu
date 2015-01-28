<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Component\Content\Model;

interface ContentInterface
{
    public function getParent();

    public function setParent($parent);

    public function getChildren();
    
    public function setChildren($children);

    public function getTitle(); 
    
    public function setTitle($title);

    public function getContentType(); 
    
    public function setContentType($contentType);

    public function getCreator(); 
    
    public function setCreator($creator);
    
    public function getChanger(); 
    
    public function setChanger($changer);

    public function getCreated(); 
    
    public function setCreated($created);

    public function setUpdated($updated);

    public function getContent();

    public function setContent($content);

    public function getName(); 
    
    public function setName($name);
}
