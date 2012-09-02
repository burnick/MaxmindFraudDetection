<?php
class Arnelceledonio_Maxmind_Block_Adminhtml_Sales_Order_Info extends Mage_Adminhtml_Block_Sales_Order_View_Info
{
    /**
     * This method has been overridden merely for the purpose of setting up a new view file
     * to be used in place of the default theme folder.
     *
     * @see app/code/core/Mage/Core/Block/Mage_Core_Block_Template#fetchView($fileName)
     */
    public function fetchView($fileName)
    {
        extract ($this->_viewVars);
        $do = $this->getDirectOutput();
 
        if (!$do) { ob_start(); }
 
        include getcwd().'/app/code/community/Arnelceledonio/Maxmind/Block/Adminhtml/Sales/Order/View/info.phtml';
 
        if (!$do) {$html = ob_get_clean(); }
        else { $html = ''; }
 
        return $html;
    }
}

?>