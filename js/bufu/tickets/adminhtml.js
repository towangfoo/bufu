/**
 * bufu_tickets admin javascript for event editing
 */

var BufuTicketsEvents = Class.create()
BufuTicketsEvents.prototype = {
    templateText : '<tr>'+
        '<td>'+
            '<input type="hidden" name="bufu_tickets[events][{{id}}][event_id]" value="{{event_id}}" />'+
            '<input type="hidden" name="bufu_tickets[events][{{id}}][delete_event]" class="delete" value="" />'+
            '<input type="text" class="required-entry input-text" name="bufu_tickets[events][{{id}}][event_date]" id="bufu_tickets_events_{{id}}_date" value="{{date}}" />'+
            '<img src="/skin/adminhtml/default/default/images/grid-cal.gif" alt="" class="v-middle" id="bufu_tickets_events_{{id}}_date_trig" title="{T{selectDate}}" style="" />'+
        '</td>'+
        '<td>'+
            '<input type="text" class="required-entry input-text" name="bufu_tickets[events][{{id}}][event_location]" value="{{location}}" />'+
        '</td>'+
        '<td>'+
            '<input type="text" class="required-entry input-text" name="bufu_tickets[events][{{id}}][event_title]" value="{{title}}" /><br />'+
            '<textarea name="bufu_tickets[events][{{id}}][event_desc]" class="input-text" wrap="off">{{desc}}</textarea>'+
        '</td>'+
        '<td>'+
            '<p><input type="text" class="validate-zero-or-greater input-text event-qty_normal" name="bufu_tickets[events][{{id}}][qty_normal]" value="{{qty_normal}}" />'+
            ' <label>[{T{Qty}}]</label></p>'+
        '</td>'+
        '<td class="price_qty">'+
            '<p><input type="text" class="required-entry validate-zero-or-greater input-text event-price_normal" name="bufu_tickets[events][{{id}}][price_normal]" value="{{price_normal}}" />'+
            ' <label>[{T{currency}}]</label></p>'+
        '</td>'+
        '<td class="price_qty">'+
            '<p><input type="text" class="required-entry validate-zero-or-greater input-text event-price_special" name="bufu_tickets[events][{{id}}][price_special]" value="{{price_special}}" />'+
            ' <label>[{T{currency}}]</label></p>'+
            '<p><input type="checkbox" id="bufu_tickets_events_{{id}}_specialPriceAvailable" name="bufu_tickets[events][{{id}}][special_price_available]" value="1" />'+
            ' <label for="bufu_tickets_events_{{id}}_specialPriceAvailable">{T{useSpecialPrice}}</label></p>'+
        '</td>'+
        '<td>'+
            '<select class="select" name="bufu_tickets[events][{{id}}][is_available]" id="bufu_tickets_events_{{id}}_is_available">'+
                '<option value="5">{T{sellingSoon}}</option>'+
                '<option value="1">{T{available}}</option>'+
                '<option value="2">{T{someLeft}}</option>'+
                '<option value="4">{T{request}}</option>'+
                '<option value="3">{T{abendkasse}}</option>'+
                '<option value="0">{T{soldOut}}</option>'+
            '</select>'+
        '</td>'+
        '<td>'+
            '<button type="button" class="scalable delete icon-btn" title="{T{deleteItem}}"><span>{T{deleteItem}}</span></button>'+
        '</td>'+
    '</tr>',
    tbody : null,
    tbodyIdentifier : '',
    templateSyntax : /(^|.|\r|\n)({{(\w+)}})/,
    translateSyntax : /(^|.|\r|\n)({T{(\w+)}})/,
    itemCount : 0,
    eventDefaults: null,

    // *************
    // initialize event row
    initialize : function(tbody, translations, defaultConfig) {
        if (!$(tbody)) {
            alert("bufu_tickets events tbody id "+ tbody +" was not found in document. Can not show events.");
        }
        this.tbodyIdentifier = tbody;
        this.tbody = $(tbody);
        this.templateText = this.translateTemplate(translations);
        this.eventDefaults = defaultConfig;
    }, // initialize

    // *************
    // add a new (existing) event row to the table of tracks
    add : function(data) {
        alertAlreadyDisplayed = false;
        this.template = new Template(this.templateText, this.templateSyntax);

        if (!data.event_id) { // new event
            data = {};
            data.event_id  = '';
            data.price_normal = this.eventDefaults.priceNormal;
            data.qty_normal = this.eventDefaults.qtyNormal;
            data.price_special = this.eventDefaults.priceSpecial;
            data.availability = this.eventDefaults.availability;
            data.specialPriceAvailable = true;
        }

        data.id = this.itemCount;

        Element.insert(this.tbody, {'bottom':this.template.evaluate(data)});

        // select field selected value
        if (data.availability >= 0) {
            availableElm = 'bufu_tickets_events_' + data.id + '_is_available';
            options = $(availableElm).options;
            for (var i=0; i < options.length; i++) {
                if (options[i].value == data.availability) {
                    options[i].selected = true;
                    Element.writeAttribute(options[i], 'selected', 'selected');
                    break;
                }
            }
        }

        // initialize calendar
        Calendar.setup({
            inputField: "bufu_tickets_events_" + this.itemCount + "_date",
            ifFormat: "%d.%m.%Y %H:%M",
            showsTime: true,
            button: "bufu_tickets_events_" + this.itemCount + "_date_trig",
            align: "Bl",
            singleClick : true
        });

        // default value for special price availability
        $("bufu_tickets_events_" + this.itemCount + "_specialPriceAvailable").checked = data.specialPriceAvailable;

        this.itemCount++;
    }, // add

    // ******************
    // remove a row from the events list, make it ready to delete when form is submitted
    remove : function(event){
        var element = $(Event.findElement(event, 'tr'));
        alertAlreadyDisplayed = false;
        if(element){
            element.down('input[type="hidden"].delete').value = '1';
            Element.select(element, 'div.flex').each(function(elm){
                elm.remove();
            });
            element.addClassName('no-display');
            element.addClassName('ignore-validate');
            element.hide();
        }
    }, // remove

    // ****************
    // register remove buttons to their job
    bindRemoveButtons : function(){
        var buttons = $$('tbody#'+ this.tbodyIdentifier +' .delete');
        for(var i=0;i<buttons.length;i++){
            if(!$(buttons[i]).binded){
                $(buttons[i]).binded = true;
                Event.observe(buttons[i], 'click', this.remove.bind(this));
            }
        }
    }, // bindRemoveButtons

    // ****************
    // translate template
    translateTemplate : function(translations){
        var translated = new Template(this.templateText, this.translateSyntax);
        return translated.evaluate(translations);
    } // translateTemplate
}
