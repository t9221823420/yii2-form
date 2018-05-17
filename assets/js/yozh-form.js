$( function () {
	
	yozh.Form = {
		activeBoolean : {
			value : 'value',
			template : null,
			templateValue : null,
		}
	};
	
	$( document ).on( 'change', 'select.yozh-nested-select', function () {
		$( $( this ).data( 'selector' ) ).load( $( this ).attr( 'url' ), 'value=' + $( this ).val() );
	} );
	
	$( document ).on( 'click', '.yozh-active-boolean', function ( e ) {
		
		var _$context = $( this );
		var _options = yozh.Form.activeBoolean;
		
		var _template = _options.template;
		var _templateValue = _options.templateValue;
		var _value = _options.value;
		var _ownerClass = _options.ownerClass;
		
		$.ajax( {
				url : _$context.attr( 'url' ),
				data : _$context.data(),
			} )
			.done( function ( _response ) {
				
				if ( _$context.data( _value ) ) {
					_$context.data( _value, 0 );
					_$context.removeClass( _ownerClass[ 0 ] ).addClass( _ownerClass[ 1 ] );
				}
				else {
					_$context.data( _value, 1 );
					_$context.removeClass( _ownerClass[ 1 ] ).addClass( _ownerClass[ 0 ] );
				}
				
				_result = parseInt( _$context.data( _value ) );
				
				$.each( _templateValue, function ( _index, _value ) {
					_template = strtr( _template, _index, _value[ _result ] );
				} );
				
				_$context.html( _template );
				
				
				var _callback = _$context.attr( 'callback' );
				
				if ( typeof _callback === 'function' ) {
					_callback( _$context, _result );
				}
				else if ( typeof _callback === 'string' ) {
					call_user_func( _callback, window, _$context, _result );
				}
				
			} );
		
		//e.preventDefault();
	} );
	
	$( document ).on( 'fileselect', '.file-input input', function ( _event, _numFiles, _label ) {
		
		var _$context = $( this );
		var _$clearButton = _$context.parents( '.file-input' ).find( '.fileinput-remove-button' );
		
		_$clearButton.find( '.counter' ).remove();
		
		if ( _numFiles == 0 ) {
			return;
		}
		else if ( _numFiles == 1 ) {
			_counterCaption = '1 file';
		}
		else {
			_counterCaption = _numFiles + ' files';
		}
		
		_$clearButton.append( '<span class="counter">' + _counterCaption + '</span>' );
	} );
	
	/*
	$(document).off().on( 'click', '.ui-accordion .ui-accordion-header a', function( e ){
		e.stopPropagation();
		e.stopImmediatePropagation();
	});
	*/
} );