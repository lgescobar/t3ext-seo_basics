/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 KO-Web | Kai Ole Hartwig <mail@ko-web.net>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

define(['jquery'], function ($) {
  'use strict';

  /**
   * @exports TYPO3/CMS/ContenthubConnector/SeoModule
   */
  var SeoModule = {};

  /**
   * Initialize markup with class chc-module-rfq and hide/collapse buttons for translation collections
   */
  SeoModule.initializeMarkupTrigger = function () {
    var seoProperties = {
      ok: {
        color: '#c0ff70',
        cssClass: 'has-success'
      },
      needsWork: {
        color: '#ffff70',
        cssClass: 'has-warning'
      },
      poor: {
        color: '#ff8040',
        cssClass: 'has-error'
      },
      clear: {
        color: 'white'
      }
    };

    var seoClassesArray = [];

    for (var prop in seoProperties) {
      if (typeof seoProperties[prop].cssClass !== 'undefined') {
        seoClassesArray.push(seoProperties[prop].cssClass);
      }
    }

    var seoClasses = seoClassesArray.join(' ');

    function checkTitleTag() {
      var $group = $(this).parent();
      var size = $(this).val().length;
      var seoClass;

      $group.removeClass(seoClasses);

      if (size > 65) {
        seoClass = 'poor';
        $group.addClass(seoProperties[seoClass].cssClass);
      } else if (size < 50 && size > 0) {
        seoClass = 'needsWork';
        $group.addClass(seoProperties[seoClass].cssClass);
      } else if (size === 0) {
        seoClass = 'clear';
      } else {
        seoClass = 'ok';
        $group.addClass(seoProperties[seoClass].cssClass);
      }

      $(this).css('backgroundColor', seoProperties[seoClass].color);
    }

    function checkKeywords() {
      var $group = $(this).parent();
      var val = $(this).val();
      var numKeywords = 0;
      var seoClass = 'ok';

      $group.removeClass(seoClasses);

      if (val.length > 0) {
        var keywords = val.match(/,/gi);
        numKeywords = 1;

        if (keywords) {
          numKeywords = keywords.length + 1;
        }
      }

      if (numKeywords > 6) {
        seoClass = 'poor';
        $group.addClass(seoProperties[seoClass].cssClass);
      } else if (numKeywords === 1) {
        seoClass = 'needsWork';
        $group.addClass(seoProperties[seoClass].cssClass);
      } else if (numKeywords === 0) {
        seoClass = 'clear';
      } else {
        seoClass = 'ok';
        $group.addClass(seoProperties[seoClass].cssClass);
      }

      $(this).css('backgroundColor', seoProperties[seoClass].color);
    }

    function checkDescription() {
      var $group = $(this).parent();
      var size = $(this).val().length;
      var seoClass;

      $group.removeClass(seoClasses);

      if (size > 150) {
        seoClass = 'poor';
        $group.addClass(seoProperties[seoClass].cssClass);
      } else if (size < 115 && size > 0) {
        seoClass = 'needsWork';
        $group.addClass(seoProperties[seoClass].cssClass);
      } else if (size === 0) {
        seoClass = 'clear';
      } else {
        seoClass = 'ok';
        $group.addClass(seoProperties[seoClass].cssClass);
      }

      $(this).css('backgroundColor', seoProperties[seoClass].color);
    }

    var $seo = $('#seo');

    $seo.on('keyup keypress', '.seo-title', checkTitleTag)
      .on('keyup keypress', '.seo-keywords', checkKeywords)
      .on('keyup keypress', '.seo-description', checkDescription);

    $seo.find('.seo-title').each(checkTitleTag);
    $seo.find('.seo-keywords').each(checkKeywords);
    $seo.find('.seo-description').each(checkDescription);
  };

  SeoModule.initializeMarkupTrigger(document);

  // expose as global object
  TYPO3.SeoModule = SeoModule;

  return SeoModule;
});
