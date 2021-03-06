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
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\RecurringPayment\Block\Adminhtml\Product\Edit\Tab\Price;

class RecurringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Element\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var \Magento\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\RecurringPayment\Block\Adminhtml\Product\Edit\Tab\Price\Recurring
     */
    protected $_testModel;

    /**
     * @var \Magento\Backend\Block\Template\Context
     */
    protected $_context;

    /**
     * @var \Magento\Event\Manager
     */
    protected $_eventManager;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_blockFactory = $this->getMock(
            'Magento\View\Element\BlockFactory', ['createBlock'], [], '', false
        );

        $this->_registry = $this->getMock(
            'Magento\Registry', [], [], '', false
        );

        $this->_eventManager = $this->getMock('Magento\Event\Manager', [], [], '', false);
        $this->_storeConfig = $this->getMock('Magento\Core\Model\Store\Config', [], [], '', false);
        $this->_context = $helper->getObject(
            'Magento\Backend\Block\Template\Context', [
                'eventManager' => $this->_eventManager,
                'storeConfig' => $this->_storeConfig
            ]
        );

        $this->_testModel = $helper->getObject(
            'Magento\RecurringPayment\Block\Adminhtml\Product\Edit\Tab\Price\Recurring', [
                'blockFactory' => $this->_blockFactory,
                'registry' => $this->_registry,
                'context' => $this->_context
            ]
        );
    }

    public function testRenderRecurringPaymentForm()
    {
        $blockMock = $this->getMock(
            'Magento\View\Element\BlockInterface',
            [
                'setNameInLayout', 'setParentElement', 'setProductEntity', 'toHtml', 'addFieldMap',
                'addFieldDependence', 'addConfigOptions'
            ]
        );
        $map = [
            ['Magento\RecurringPayment\Block\Adminhtml\Payment\Edit\Form', [], $blockMock],
            ['Magento\Backend\Block\Widget\Form\Element\Dependence', [], $blockMock]
        ];
        $paymentElement = $this->getMock('Magento\Data\Form\Element\AbstractElement', [], [], '', false);
        $this->_storeConfig->expects($this->any())->method('getConfig')->will($this->returnValue(true));

        $this->_testModel->render($paymentElement);

        $product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->_registry->expects($this->once())->method('registry')->will($this->returnValue($product));

        $this->_blockFactory->expects($this->any())->method('createBlock')->will($this->returnValueMap($map));

        $blockMock->expects($this->any())->method('setNameInLayout');
        $blockMock->expects($this->once())->method('setProductEntity')->with($product);
        $blockMock->expects($this->exactly(2))->method('toHtml')->will($this->returnValue('html'));
        $blockMock->expects($this->once())->method('addConfigOptions')->with(['levels_up' => 2]);

        $this->assertEquals('htmlhtml', $this->_testModel->getElementHtml());
    }
}
