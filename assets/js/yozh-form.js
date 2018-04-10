Yozh.Form = {
    activeBoolean:{
        value: 'value',
        template: null,
        templateValue: null,
    }
};

$(function () {

    $(document).on('change', 'select.yozh-nested-select', function () {
        $($(this).data('selector')).load($(this).attr('url'), 'value=' + $(this).val());
    });

    $(document).on('click', '.yozh-active-boolean', function (e) {

        var _$owner = $(this);
        var _options = Yozh.Form.activeBoolean;

        var _template = _options.template;
        var _templateValue = _options.templateValue;
        var _value = _options.value;
        var _ownerClass = _options.ownerClass;

        $.ajax({
            url: _$owner.attr('url'),
            data: _$owner.data(),
        })
            .done(function ( _response ) {

                if ( _$owner.data(_value) ) {
                    _$owner.data(_value, 0);
                    _$owner.removeClass( _ownerClass[1] ).addClass( _ownerClass[0] );
                }
                else {
                    _$owner.data(_value, 1);
                    _$owner.removeClass( _ownerClass[0] ).addClass( _ownerClass[1] );
                }

                _result = parseInt( _$owner.data( _value ) );

                $.each(_templateValue, function( _index, _value ){
                    _template = strtr(_template, _index, _value[_result] );
                });

                _$owner.html(_template);



                var _callback = _$owner.attr('callback');

                if (typeof _callback === 'function') {
                    _callback(_$owner, _result);
                }
                else if (typeof _callback === 'string') {
                    call_user_func(_callback, window, _$owner, _result);
                }

            });

        //e.preventDefault();
    });

    $(document).on('fileselect', '.file-input input', function( _event, _numFiles, _label ) {

        var _$owner = $(this);
        var _$clearButton = _$owner.parents('.file-input').find('.fileinput-remove-button');

        _$clearButton.find('.counter').remove();

        if( _numFiles == 0 ){
            return;
        }
        else if ( _numFiles == 1 ) {
            _counterCaption = '1 file';
        }
        else{
            _counterCaption =  _numFiles + ' files';
        }

        _$clearButton.append( '<span class="counter">' + _counterCaption + '</span>' );
    });

    /*
    $(document).off().on( 'click', '.ui-accordion .ui-accordion-header a', function( e ){
        e.stopPropagation();
        e.stopImmediatePropagation();
    });
    */
});