<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Customer\Model\Renderer;

use Magento\Data\Form\Element\AbstractElement;

/**
 * Region field renderer
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Region implements \Magento\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * Country region collections
     *
     * Structure:
     * array(
     *      [$countryId] => \Magento\Data\Collection\Db
     * )
     *
     * @var array
     */
    static protected $_regionCollections;

    /**
     * Adminhtml data
     *
     * @var \Magento\Escaper
     */
    protected $_escaper = null;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;

    /**
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Escaper $escaper
     */
    public function __construct(
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Escaper $escaper
    ) {
        $this->_countryFactory = $countryFactory;
        $this->_directoryHelper = $directoryHelper;
        $this->_escaper = $escaper;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $countryId = false;
        $isRegionRequired = false;
        if ($country = $element->getForm()->getElement('country_id')) {
            $countryId = $country->getValue();
            $isRegionRequired = $this->_directoryHelper->isRegionRequired($countryId);
        }

        $html = '<div class="field field-region ' . (($isRegionRequired) ? 'required' : '') . '">'."\n";

        $regionCollection = false;
        if ($countryId) {
            if (!isset(self::$_regionCollections[$countryId])) {
                self::$_regionCollections[$countryId] = $this->_countryFactory->create()
                    ->setId($countryId)
                    ->getLoadedRegionCollection()
                    ->toOptionArray();
            }
            $regionCollection = self::$_regionCollections[$countryId];
        }

        $regionId = intval($element->getForm()->getElement('region_id')->getValue());

        $htmlAttributes = $element->getHtmlAttributes();
        foreach ($htmlAttributes as $key => $attribute) {
            if ('type' === $attribute) {
                unset($htmlAttributes[$key]);
                break;
            }
        }

        // Output two elements - for 'region' and for 'region_id'.
        // Two elements are needed later upon form post - to properly set data to address model,
        // otherwise old value can be left in region_id attribute and saved to DB.
        // Depending on country selected either 'region' (input text) or 'region_id' (selectbox) is visible to user
        $regionHtmlName = $element->getName();
        $regionIdHtmlName = str_replace('region', 'region_id', $regionHtmlName);
        $regionHtmlId = $element->getHtmlId();
        $regionIdHtmlId = str_replace('region', 'region_id', $regionHtmlId);

        if ($isRegionRequired) {
            $element->addClass('required-entry');
        }

        if ($regionCollection && count($regionCollection) > 0) {
            $elementClass = $element->getClass();
            $html.= '<label class="label" for="' . $regionIdHtmlId . '"><span>' . $element->getLabel() . '</span>'
                . '</label>';
            $html.= '<div class="control">';

            $html .= '<select id="' . $regionIdHtmlId . '" name="' . $regionIdHtmlName . '" '
                 . $element->serialize($htmlAttributes) .'>' . "\n";
            foreach ($regionCollection as $region) {
                $selected = ($regionId==$region['value']) ? ' selected="selected"' : '';
                $regionVal = (0 == $region['value']) ? '' : (int)$region['value'];
                $html.= '<option value="' . $regionVal . '"' . $selected . '>'
                    . $this->_escaper->escapeHtml(__($region['label']))
                    . '</option>';
            }
            $html.= '</select>' . "\n";

            $html .= '<input type="hidden" name="' . $regionHtmlName . '" id="' . $regionHtmlId . '" value=""/>';

            $html.= '</div>';
            $element->setClass($elementClass);
        } else {
            $html.= '<label class="label" for="' . $regionHtmlId . '"><span>'
                . $element->getLabel()
                . '</span></label>';
            $html.= '<div class="control">';
            $html .= '<input id="' . $regionHtmlId . '" name="' . $regionHtmlName
                . '" value="' . $element->getEscapedValue() . '" '
                . $element->serialize($htmlAttributes) . "/>" . "\n";
            $html .= '<input type="hidden" name="' . $regionIdHtmlName . '" id="' . $regionIdHtmlId . '" value=""/>';
            $html .= '</div>'."\n";
        }
        $html.= '</div>'."\n";
        return $html;
    }
}
