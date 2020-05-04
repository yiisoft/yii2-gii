(function ($) {

    /**
     * ReIndexes attributes such as id, name, etc.
     * It indexes all element from 0 to total_elements-1
     * For example, if we are cloning some element and it has an id value `example1`, then the element after this will have id `example2`
     * @param rows_selector
     */
    var reIndexAttributes = function(rows_selector) {
        var props = $(rows_selector);
        var regex = /^(.+?)(\d+)$/i;
        var name_regex = /^(.+?)(\d+)([\[\]]+)$/i;
        var cur_index = 0;
        props.each(function () {

            var id = this.id || "";
            var match = id.match(regex) || [];
            if (match.length === 3) {
                this.id = match[1] + (cur_index);
            }

            $(this).find("*")
                .each(function() {
                    var id = this.id || "";
                    var match = id.match(regex) || [];
                    if (match.length === 3) {
                        this.id = match[1] + (cur_index);
                    }

                    var target = $(this).attr('data-target') || "";
                    match = target.match(regex) || [];
                    if (match.length === 3) {
                        $(this).attr('data-target', match[1] + (cur_index));
                    }

                    target = $(this).attr('aria-labelledby') || "";
                    match = target.match(regex) || [];
                    if (match.length === 3) {
                        $(this).attr('aria-labelledby', match[1] + (cur_index));
                    }

                    target = $(this).attr('name') || "";
                    match = target.match(name_regex) || [];
                    if (match.length === 4) {
                        $(this).attr('name', match[1] + (cur_index) + match[3]);
                    }

                    target = $(this).attr('data-index') || "";
                    if(target !== ''){
                        $(this).attr('data-index', cur_index);
                        if($(this)[0].nodeName === 'SPAN'){
                            $(this).html(cur_index);
                        }
                    }
                });
            cur_index++;
            $('.js-property-name').trigger('change');
        });
    }

    /**
     * Removes values from all type of input element
     * @param element
     * @returns {*}
     */
    var truncateValues = function(element){
        $(element).find('input:text, input:password, input:file, select, textarea')
            .each(function() {
                $(this).val('');
            });

        $(element).find('input:radio, input:checkbox').each(function() {
            $(this).removeAttr('checked');
            $(this).removeAttr('selected');
        });

        return element;
    }


    /**
     * Give an attribute to anchor tag 'js-add' and the value of that attribute
     * to be his    parent selector which is to be copied and to be added after it.
     * For example if you provide <a  js-add=".js-field_row">text</a>
     * On click of this element will copy its parent element having class js-field_row
     * and will after it.
     */
    $(document).on('click', 'a[js-add]', function () {
        var element_to_clone_selector = $(this).attr('js-add');
        var element_to_clone = $(this).parents(element_to_clone_selector);
        var duplicate_element = element_to_clone.clone();
        duplicate_element = truncateValues(duplicate_element);
        element_to_clone.after(duplicate_element);
        reIndexAttributes('.js-field_row');
    });

    /*
     * Give an attribute to anchor tag 'js-remove' and the value of that attribute
     * to be his    parent selector which is to be deleted.
     * For example if you provide <a  js-remove=".js-field_row">text</a>
     * On click of this element will remove its parent element having class js-field_row
     * */
    $(document).on('click', 'a[js-remove]', function () {
        var removable_element_selector = $(this).attr('js-remove');
        var removable_element = $(this).parents(removable_element_selector);
        //count all siblings, if its more than 1, delete the selected element.
        var total_row = removable_element.siblings(removable_element_selector).length;
        if (total_row > 0) {
            removable_element.remove();
            reIndexAttributes();
        } else {
            alert('There should be at least one row');
        }
    });

})(jQuery);
