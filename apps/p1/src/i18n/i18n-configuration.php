<?php

namespace p1\i18n;

use Exception;
use i18n;

require_once "../../vendor/autoload.php";

class I18nConfiguration {
  private static I18nConfiguration $instance;
  private i18n $i18n;

  private function __construct() {
    $this->i18n = $this->initI18n();
  }

  private function initI18n(): i18n {
    $i18n = new i18n();
    $i18n->setFilePath("i18n/bundle_{LANGUAGE}.yml");
    $i18n->setCachePath("/tmp/cache");
//        $i18n->setForcedLang("pl");
    $i18n->setFallbackLang("en");
    $i18n->setSectionSeparator("_");
    $i18n->setMergeFallback(true);
    $i18n->setPrefix("L");
    $i18n->init();
    return $i18n;
  }

  // SINGLETON SPECIFIC FUNCTIONS

  /**
   * Singleton cloning is forbidden.
   * @return void
   */
  private function __clone() {}

  /**
   * Singleton deserialization is forbidden.
   * @throws Exception
   */
  public function __wakeup() {
    throw new Exception("Cannot deserialize singleton");
  }

  static function instance(): I18nConfiguration {
    if (!isset(self::$instance)) {
      self::$instance = new static();
    }
    return self::$instance;
  }
}