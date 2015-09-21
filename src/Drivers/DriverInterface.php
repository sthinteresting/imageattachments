<?php
namespace S12g\ImageAttachments\Drivers;

interface DriverInterface {
  public function saveImage($filename);
  public function getConfigItems();
}