<?php 
namespace Creode\Facebook;


/*
Represents a facebook post.
*/
class CreodeFacebookData
{

      private $message=null;
      private $name=null;
      private $numLikes=null;
      private $picture=null;
      private $link=null;
      private $createdTime=null;
      private $uuid=null;

      public function setMessage($message)
      {
          $this->message = $message;
      }

      public function getMessage()
      {
           return $this->message;
      }

      public function setName($name)
      {
          $this->name = $name;
      }

      public function getName()
      {
           return $this->name;
      }

      public function setNumLikes($numLikes)
      {
          $this->numLikes = $numLikes;
      }

      public function getNumLikes()
      {
           return $this->numLikes;
      }

      public function setPicture($picture)
      {
          $this->picture = $picture;
      }

      public function getPicture()
      {
           return $this->picture;
      }

      public function setLink($link)
      {
          $this->link = $link;
      }

      public function getLink()
      {
           return $this->link;
      }

      public function setCreatedTime($createdTime)
      {
          $this->createdTime = $createdTime;
      }

      public function getCreatedTime()
      {
           return $this->createdTime;
      }

      public function setUuid($uuid)
      {
          $this->uuid = $uuid;
      }

      public function getUuid()
      {
          return $this->uuid;
      }

}
?>