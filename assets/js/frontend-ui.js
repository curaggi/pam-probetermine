(function($){
    $(function(){

        // Enhance probe form (device select to list view)
        function enhanceDeviceSelect(form){
            var $select = $('select[name="device_id"]', form);
            if(!$select.length || $select.data('pam-pt-enhanced')) return;

            $select.data('pam-pt-enhanced', true);

            var options = [];
            $select.find('option').each(function(){
                var $opt = $(this);
                var val = $opt.attr('value');
                var text = $.trim($opt.text());
                if(!val){ return; }
                options.push({
                    value: val,
                    label: text,
                    icon: guessIconFromLabel(text.toLowerCase())
                });
            });

            if(!options.length) return;

            $select.addClass('pam-pt-device-select-hidden');

            var $wrapper = $('<div class="pam-pt-device-wrapper"></div>');
            var $labelRow = $('<div class="pam-pt-device-list-label-row"></div>');
            var $label = $('<div class="pam-pt-label"></div>').text($select.data('label') || 'Gerät');
            var $hint = $('<div class="pam-pt-device-hint"></div>').text('Bitte auswählen');
            $labelRow.append($label, $hint);

            var $list = $('<div class="pam-pt-device-list"></div>');

            options.forEach(function(opt, idx){
                var $item = $('<div class="pam-pt-device-option" tabindex="0"></div>')
                    .attr('data-value', opt.value);

                var $iconWrap = $('<div class="pam-pt-device-icon"></div>')
                    .append($('<span></span>').addClass(opt.icon));

                var $labelWrap = $('<div class="pam-pt-device-option-icon-label"></div>');
                var $name = $('<div class="pam-pt-device-name"></div>').text(opt.label);
                $labelWrap.append($iconWrap, $name);

                var $check = $('<div class="pam-pt-device-check">✓</div>');

                $item.append($labelWrap, $check);

                $item.on('click keypress', function(e){
                    if(e.type === 'keypress' && e.which !== 13 && e.which !== 32){ return; }
                    $list.find('.pam-pt-device-option').removeClass('is-active');
                    $item.addClass('is-active');
                    $select.val(opt.value).trigger('change');
                });

                // Preselect if matches current value
                if($select.val() === opt.value){
                    $item.addClass('is-active');
                }

                $list.append($item);
            });

            $wrapper.append($labelRow, $list);
            $select.after($wrapper);
        }

        function guessIconFromLabel(label){
            if(label.indexOf('stoß') !== -1 || label.indexOf('stoss') !== -1 || label.indexOf('shock') !== -1){
                return 'pamicon-bolt';
            }
            if(label.indexOf('laser') !== -1){
                return 'pamicon-laser';
            }
            if(label.indexOf('kryo') !== -1 || label.indexOf('cryo') !== -1 || label.indexOf('eis') !== -1 || label.indexOf('cold') !== -1){
                return 'pamicon-snow';
            }
            if(label.indexOf('heat') !== -1 || label.indexOf('thermo') !== -1 || label.indexOf('heit') !== -1){
                return 'pamicon-heat';
            }
            if(label.indexOf('ems') !== -1 || label.indexOf('sculpt') !== -1 || label.indexOf('stimu') !== -1){
                return 'pamicon-wave';
            }
            if(label.indexOf('massage') !== -1){
                return 'pamicon-hand';
            }
            if(label.indexOf('train') !== -1 || label.indexOf('fitness') !== -1){
                return 'pamicon-dumbbell';
            }
            return 'pamicon-dot';
        }

        // Attach enhancement to all probe forms
        $('form.pam-pt-form').each(function(){
            enhanceDeviceSelect($(this));
        });

    });
})(jQuery);
