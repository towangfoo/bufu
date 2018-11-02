<?php
/**
 * Copyright © 2017 Wyomind. All rights reserved.
 */
class Wyomind_Watchlog_Block_Adminhtml_System_Config_Form_Field_Cron extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) 
    {
        $html = "";
        $html .= "<input class=' input-text' type='hidden' id='" . $element->getHtmlId() . "' "
                . "name='" . $element->getName() . "' "
                . "value='" . $element->getEscapedValue() . "' '" . $element->serialize($element->getHtmlAttributes()) . "/>";
        $html .= "
<script>
    document.observe('dom:loaded', function(){
        if(!$('" . $element->getHtmlId() . "').value.isJSON())$('" . $element->getHtmlId() . "').value='{\"days\":[],\"hours\":[]}';
        cron=$('" . $element->getHtmlId() . "').value.evalJSON();
        cron.days.each(function(d){
            if($('d-'+d)){
                $('d-'+d).checked=true;
                $('d-'+d).ancestors()[0].addClassName('checked');
            }
        })
        cron.hours.each(function(h){
            if( $('h-'+h.replace(':',''))){
                $('h-'+h.replace(':','')).checked=true;
                $('h-'+h.replace(':','')).ancestors()[0].addClassName('checked');
            }
        })
        
        $$('.cron-box').each(function(e){
            e.observe('click',function(){
                if(e.checked)e.ancestors()[0].addClassName('checked');
                else e.ancestors()[0].removeClassName('checked');
                d=new Array
                $$('.cron-d-box INPUT').each(function(e){
                    if(e.checked) d.push(e.value)
                })
                h=new Array;
                $$('.cron-h-box INPUT').each(function(e){
                    if(e.checked) h.push(e.value)
                })
                
                $('" . $element->getHtmlId() . "').value=Object.toJSON({days:d,hours:h})
            }) 
        })
    })
</script>
";
        $html .= "
<style>
    .morning .cron-h-box{
        border: 1px solid #AFAFAF;
        border-radius: 3px 3px 3px 3px;
        margin: 2px;
        padding: 0 3px;
        background:#efefef;
    }
    .afternoon .cron-h-box{
        border: 1px solid #AFAFAF;
        border-radius: 3px 3px 3px 3px;
        margin: 2px;
        padding: 0 3px;
        background:#efefef;
    }
    .morning-half .cron-h-box{
        border: 1px solid #AFAFAF;
        border-radius: 3px 3px 3px 3px;
        margin: 2px;
        padding: 0 3px;
        background:#efefef;
    }
    .afternoon-half .cron-h-box{
        border: 1px solid #AFAFAF;
        border-radius: 3px 3px 3px 3px;
        margin: 2px;
        padding: 0 3px;
        background:#efefef;
    }
    .cron-d-box{
        background:#efefef;
        border: 1px solid #AFAFAF;
        border-radius: 3px 3px 3px 3px;
        margin: 2px;
        padding: 0 3px;
    }
    .checked{
        background-color: #EFFFF0!important;
    }
</style>";

        $html .= "<table style='width:600px !important'>
            <thead> 
                <tr><th>Days of the week</th><th width='20'></th><th colspan='4'>Hours of the day</th></tr>
            </thead>
            <tr>
                <td width='300'>
                    <div class='cron-d-box'><input class='cron-box' value='Monday' id='d-Monday' type='checkbox'/><label for='d-Monday'>Monday</label></div>
                    <div class='cron-d-box'><input class='cron-box' value='Tuesday' id='d-Tuesday' type='checkbox'/><label for='d-Tuesday'>Tuesday</label></div>
                    <div class='cron-d-box'><input class='cron-box' value='Wednesday' id='d-Wednesday' type='checkbox'/><label for='d-Wednesday'>Wednesday</label></div>
                    <div class='cron-d-box'><input class='cron-box' value='Thursday' id='d-Thursday' type='checkbox'/><label for='d-Thursday'>Thursday</label></div>
                    <div class='cron-d-box'><input class='cron-box' value='Friday' id='d-Friday' type='checkbox'/><label for='d-Friday'>Friday</label></div>
                    <div class='cron-d-box'><input class='cron-box' value='Saturday' id='d-Saturday' type='checkbox'/><label for='d-Saturday'>Saturday</label></div>
                    <div class='cron-d-box'><input class='cron-box' value='Sunday' id='d-Sunday' type='checkbox'/><label for='d-Sunday'>Sunday</label></div>
                </td>
                <td></td>
                <td width='150' class='morning-half'>
                    <div class='cron-h-box'><input class='cron-box' value='00:00' id='h-0000' type='checkbox'/><label for='h-0000'>00:00 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='01:00' id='h-0100' type='checkbox'/><label for='h-0100'>01:00 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='02:00' id='h-0200' type='checkbox'/><label for='h-0200'>02:00 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='03:00' id='h-0300' type='checkbox'/><label for='h-0300'>03:00 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='04:00' id='h-0400' type='checkbox'/><label for='h-0400'>04:00 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='05:00' id='h-0500' type='checkbox'/><label for='h-0500'>05:00 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='06:00' id='h-0600' type='checkbox'/><label for='h-0600'>06:00 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='07:00' id='h-0700' type='checkbox'/><label for='h-0700'>07:00 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='08:00' id='h-0800' type='checkbox'/><label for='h-0800'>08:00 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='09:00' id='h-0900' type='checkbox'/><label for='h-0900'>09:00 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='10:00' id='h-1000' type='checkbox'/><label for='h-1000'>10:00 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='11:00' id='h-1100' type='checkbox'/><label for='h-1100'>11:00 AM</label></div>
                </td>
                <td width='150' class='morning'>
                    <div class='cron-h-box'><input class='cron-box' value='00:30' id='h-0030' type='checkbox'/><label for='h-0030'>00:30 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='01:30' id='h-0130' type='checkbox'/><label for='h-0130'>01:30 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='02:30' id='h-0230' type='checkbox'/><label for='h-0230'>02:30 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='03:30' id='h-0330' type='checkbox'/><label for='h-0330'>03:30 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='04:30' id='h-0430' type='checkbox'/><label for='h-0430'>04:30 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='05:30' id='h-0530' type='checkbox'/><label for='h-0530'>05:30 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='06:30' id='h-0630' type='checkbox'/><label for='h-0630'>06:30 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='07:30' id='h-0730' type='checkbox'/><label for='h-0730'>07:30 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='08:30' id='h-0830' type='checkbox'/><label for='h-0830'>08:30 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='09:30' id='h-0930' type='checkbox'/><label for='h-0930'>09:30 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='10:30' id='h-1030' type='checkbox'/><label for='h-1030'>10:30 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='11:30' id='h-1130' type='checkbox'/><label for='h-1130'>11:30 AM</label></div>
                </td>
                <td width='150' class='afternoon-half'>
                    <div class='cron-h-box'><input class='cron-box' value='12:00' id='h-1200' type='checkbox'/><label for='h-1200'>12:00 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='13:00' id='h-1300' type='checkbox'/><label for='h-1300'>01:00 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='14:00' id='h-1400' type='checkbox'/><label for='h-1400'>02:00 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='15:00' id='h-1500' type='checkbox'/><label for='h-1500'>03:00 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='16:00' id='h-1600' type='checkbox'/><label for='h-1600'>04:00 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='17:00' id='h-1700' type='checkbox'/><label for='h-1700'>05:00 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='18:00' id='h-1800' type='checkbox'/><label for='h-1800'>06:00 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='19:00' id='h-1900' type='checkbox'/><label for='h-1900'>07:00 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='20:00' id='h-2000' type='checkbox'/><label for='h-2000'>08:00 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='21:00' id='h-2100' type='checkbox'/><label for='h-2100'>09:00 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='22:00' id='h-2200' type='checkbox'/><label for='h-2200'>10:00 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='23:00' id='h-2300' type='checkbox'/><label for='h-2300'>11:00 PM</label></div>
                </td>
                <td width='150' class='afternoon'>
                    <div class='cron-h-box'><input class='cron-box' value='12:30' id='h-1230' type='checkbox'/><label for='h-1230'>12:30 AM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='13:30' id='h-1330' type='checkbox'/><label for='h-1330'>01:30 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='14:30' id='h-1430' type='checkbox'/><label for='h-1430'>02:30 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='15:30' id='h-1530' type='checkbox'/><label for='h-1530'>03:30 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='16:30' id='h-1630' type='checkbox'/><label for='h-1630'>04:30 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='17:30' id='h-1730' type='checkbox'/><label for='h-1730'>05:30 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='18:30' id='h-1830' type='checkbox'/><label for='h-1830'>06:30 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='19:30' id='h-1930' type='checkbox'/><label for='h-1930'>07:30 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='20:30' id='h-2030' type='checkbox'/><label for='h-2030'>08:30 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='21:30' id='h-2130' type='checkbox'/><label for='h-2130'>09:30 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='22:30' id='h-2230' type='checkbox'/><label for='h-2230'>10:30 PM</label></div>
                    <div class='cron-h-box'><input class='cron-box' value='23:30' id='h-2330' type='checkbox'/><label for='h-2330'>11:30 PM</label></div>
                </td>
            </tr>
        </table>";
        
        $html .= $element->getAfterElementHtml();
        
        return $html;
    }
}