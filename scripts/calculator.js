(function($){
var NightOwl={
	answer:false,							// ANSWER CAN BE RETRIEVED IN CALLBACK FUNCTIONS USING THIS.ANSWER	
	$activeCalc:false,						// MARKS THE ACTIVE CALCULATOR FOR DRAG INDICATION
	$activeMini:false,						// INDICATES ACTIVE ASSIGNED INPUT CALCULATOR
	defaults:{
		// BASIC/SCIENTIFIC CALCULATOR SETTINGS
		skin:1,								// SETUP SKIN CSS
		type:'standard',					// TYPE OF NIGHTOWL CALCULATOR (STANDARD OR MINI)
		solar:true, 						// DISPLAY SOLAR PANELS
		title:'PreTally',					// TITLE OF CALCULATOR
		subline:'A UroGulf Concern', 	// SUBLINE TEXT
		toggle:true,						// ENABLE TOGGLING
		start_mode:'basic',			// MODE TO START IN
		mode_switch_speed:200,				// ANIMATION SPEED USED WHEN TOGGLING MODES

		// MINI CALCULATOR SETTINGS
		equation:false,						// ENTER EQUATION TO GENERATE MINI CALCULATOR

		// MISC SETTINGS
		assign_field:false,					// CLICKING ON INPUT WITH THIS CLASS OPENS/CLOSES THIS CALCULATOR
		show_answer:false,					// ONLY USE ASSIGNED INPUT FOR ANSWER
		match_pos:'right_down',				// CALCULATOR POSITION TO POSITION OF ASSIGNED FIELD [_UP,_DOWN,_RIGHT,_LEFT]
		pos_mode:'relative',				// RELATIVE, ABSOLUTE OR FIXED
		offset:'0,0',						// OFFSET CALCULATOR WHEN POSITIONING (X,Y)
		drag:true,							// DETERMINES IF CALCULATOR IS DRAGGABLE
		topIndex:999999,					// USED TO PULL DRAGGED CALCULATORS TO THE TOP
		pos_moved:false,					// DETERMINES IF THE CALCULATOR HAS BEEN DRAGGED ALREADY		
		result_animation:false,				// ANIMATION TO PLAY ON RESULT (BUILD AS MANY AS YOU WANT IN CSS)
		speed:'150,100',					// SPEED IN/OUT
		on_solve:false		  				// CALLBACK FUNCTION
	},	
	
/***********************************************************************************************/
// INITIALIZE
/***********************************************************************************************/
init:function(options){

	// CLOSE CALCULATORS ASSIGNED TO INPUTS
	$(document).off('click.NightOwl touchstart.NightOwl').on('click.NightOwl touchstart.NightOwl',function(e){
		var $target=$(e.target);
		// CLOSE ALL CALCS
		NightOwl.closeCalcs($target);
	});
	
	// LOOP
	return this.each(function(){
		// MERGE USER DATA WITH DEFAULTS
		var	$this=$(this),
			o=options,	
			mergedData=$.extend({}, NightOwl.defaults, o),
			calcD=$this.data('nightowl'),
			cD=calcD!=undefined ? calcD : false;
			$.data($this, $.extend({}, mergedData, !cD ? {} : cD||{}));
		$this.data('nightowl',$.data($this));
		var noD=$this.data('nightowl');
		noD.AniStat=0;

		// BUILD HTML
		NightOwl.buildHTML($this, noD);
		
		// SETUP CALCULATOR DRAGGABILITY
		if(noD.drag) NightOwl.addDrag($this, false);	

		/**********************************************************************************/
		// STANDARD CALCUALTORS (BASIC/SCIENTIFIC)
		/**********************************************************************************/
		if(noD.type.removeWS().toLowerCase()==='standard'){
			// BRING TO FRONT, CLEAR INPUTS AND FOCUS ON CALCULATOR INPUT
			$this.css('z-index',noD.topIndex.pF()).find('input[type=text]').add('input.no_oldresult').val('');
			$this.find('input.no_calc_field').focus();

			// SETUP KEY CLICK EVENTS
			$this.on('mouseup touchend',function(e){
				var $key=e.target,
					$calc=$($key).parents('form.night_owl_calc');
					
				switch($key){
					case $calc.find('input.key_1')[0] 		: NightOwl.cdef($calc, 1); break;
					case $calc.find('input.key_2')[0]		: NightOwl.cdef($calc, 2); break;
					case $calc.find('input.key_3')[0]		: NightOwl.cdef($calc, 3); break;
					case $calc.find('input.key_4')[0]		: NightOwl.cdef($calc, 4); break;
					case $calc.find('input.key_5')[0] 		: NightOwl.cdef($calc, 5); break;
					case $calc.find('input.key_6')[0] 		: NightOwl.cdef($calc, 6); break;
					case $calc.find('input.key_7')[0]		: NightOwl.cdef($calc, 7); break;
					case $calc.find('input.key_8')[0]		: NightOwl.cdef($calc, 8); break;
					case $calc.find('input.key_9')[0] 		: NightOwl.cdef($calc, 9); break;
					case $calc.find('input.key_0')[0] 		: NightOwl.cdef($calc, 0); break;
					case $calc.find('input.key_pi')[0] 		: NightOwl.cdef($calc, Math.PI); break;
					case $calc.find('input.key_decimal')[0] 	: NightOwl.cdef($calc, '.'); break;
					case $calc.find('input.key_add')[0] 		: NightOwl.cdef($calc, '+'); break;												
					case $calc.find('input.key_e')[0] 		: NightOwl.cdef($calc, 'e'); break;
					case $calc.find('input.key_minus')[0] 	: NightOwl.cdef($calc, '-'); break;
					case $calc.find('input.key_multiply')[0] : NightOwl.cdef($calc, '*'); break;
					case $calc.find('input.key_divide')[0] 	: NightOwl.cdef($calc, '/'); break;												
					case $calc.find('input.key_parr')[0] 	: NightOwl.cdef($calc, ')'); break;												
					case $calc.find('input.key_parl')[0] 	: NightOwl.cdef($calc, '('); break;												
					case $calc.find('input.key_enter')[0] 	: NightOwl.ghij($calc, 1); break;												
					case $calc.find('input.key_square')[0] 	: NightOwl.ghij($calc, 2); break;												
					case $calc.find('input.key_squareroot')[0]: NightOwl.ghij($calc, 3); break;												
					case $calc.find('input.key_changesign')[0]: NightOwl.ghij($calc, 4); break;												
					case $calc.find('input.key_natlog')[0] 	: NightOwl.ghij($calc, 5); break;												
					case $calc.find('input.key_natanti')[0] 	: NightOwl.ghij($calc, 6); break;												
					case $calc.find('input.key_1x')[0] 		: NightOwl.ghij($calc, 7); break;												
					case $calc.find('input.key_log')[0] 		: NightOwl.ghij($calc, 8); break;												
					case $calc.find('input.key_antil')[0] 	: NightOwl.ghij($calc, 9); break;												
					case $calc.find('input.key_atan')[0] 	: NightOwl.ghij($calc, 10); break;												
					case $calc.find('input.key_acos')[0]		: NightOwl.ghij($calc, 11); break;												
					case $calc.find('input.key_asin')[0] 	: NightOwl.ghij($calc, 12); break;												
					case $calc.find('input.key_tan')[0] 		: NightOwl.ghij($calc, 14); break;												
					case $calc.find('input.key_cos')[0] 		: NightOwl.ghij($calc, 15); break;
					case $calc.find('input.key_sin')[0] 		: NightOwl.ghij($calc, 16); break;
					case $calc.find('input.key_percent')[0] 	: NightOwl.ghij($calc, 17); break;
					case $calc.find('input.key_ppm')[0] 		: NightOwl.ghij($calc, 18); break;
					case $calc.find('input.key_x')[0] 		: NightOwl.ghij($calc, 20); break;
					case $calc.find('input.key_xy')[0] 		: NightOwl.ghij($calc, 21); break;
					case $calc.find('input.key_root')[0] 	: NightOwl.ghij($calc, 22); break;
					case $calc.find('input.key_MS')[0] 		: NightOwl.memory($calc, 1); break;
					case $calc.find('input.key_MR')[0] 		: NightOwl.memory($calc, 2); break;
					case $calc.find('input.key_cls')[0] 		: NightOwl.memory($calc, 3); break;
					case $calc.find('input.key_backspace')[0] : NightOwl.backspace($calc); break;
					case $calc.find('label.link_rad')[0] : $this.find('input.key_radians').click(); e.preventDefault; break;
					case $calc.find('label.link_grad')[0]: $this.find('input.key_gradient').click(); e.preventDefault(); break;
					case $calc.find('label.link_deg')[0] : $this.find('input.key_degree').click(); e.preventDefault(); break;
					case $calc.find('.key_radians')[0] || $('.key_degree')[0] || $('.key_gradient')[0] : $this.focus(); break;				
				};
			});
		
			// DISPLAY RESULTS ON RESULTS FIELD FOCUS
			$this.find('input.no_results').on('focus',function(e){ NightOwl.display($this, $(this).val()); e.preventDefault; });
		
			// DECIMAL INDICATION HELPER		
			$this.find('select.no_dec_select').on('change',function(){ 
				var $calc=$(this).parents('form.night_owl_calc'),
					$oldR=$calc.find('input.no_oldresult');
				if($oldR.val()!=='') NightOwl.result($calc, $oldR.val());
				$calc.find('input.no_calc_field').focus();
			});
		
			// SETUP STATUS FOR RADIO BUTTONS
			$this.find('div.no_rb').on('mouseover touchstart',function(e){
				switch(e.target){
					case $('input.link_rad')[0]: self.status='Radians'; e.preventDefault(); break;
					case $('input.link_deg')[0]: self.status='Degrees'; e.preventDefault(); break;
					case $('input.link_grad')[0]: self.status='Gradients'; e.preventDefault(); break;			
				};
			});

			// KEYDOWN ON MAIN CALCULATOR FIELD
			$this.find('input.no_calc_field').on('keypress',function(e){ 
				var $this=$(this);			
		
				// IF USING ASSIGNED FIELD, CLOSE CALCULATOR WHEN TABBING OUT OF CALCULATOR SCREEN INPUT
				if(e.keyCode===9 && noD.assign_field){
					var $calc=$this.parents('form.night_owl_calc:first').parent();
					$calc.stop(true,false).animate({opacity:0},{duration:noD.speed.split(',')[1].pF(),queue:false,
						complete:function(){ $(this).css('display','none'); }});
				};

				if(this.value.match(/^[a-zA-Z]+$/)){ 
					$this.val(' '); 
					return false; 
				}else{ 
					if(e.keyCode==13) NightOwl.ghij($this.parents('form.night_owl_calc'), 1);
					return true; 
				};
								
			}).on('change',function(){ 
				var $calc=$(this).parents('form.night_owl_calc');
				$calc.find('input.key_enter').click();
			});

			// TOGGLE MODES
			noD.mode=noD.start_mode;
			if(noD.toggle.isB()){
				$this.find('input.no_key_toggle')
					.on('mouseup touchend',function(e){
						var $calc=$(this).parents('form.night_owl_calc:first'),
							noD=$calc.parent().data('nightowl'),
							$keysCont=$calc.find('div.no_main_keys'),
							$sciKeys=$keysCont.find('input[type=button]').not('input.key_basic'),
							$basKeys=$keysCont.find('input.key_basic');
	
						// IN BASIC MODE
						if(noD.mode==='basic'){
							$(this).html('&#x25BC').val($(this).html());					
							$sciKeys.css('display','inline'); 
							$keysCont.removeClass('no_basic_mode');	
							$sciKeys.stop(true,false).animate({opacity:1},{duration:noD.mode_switch_speed.pF(),queue:false,complete:function(){ 
								// IF ASSIGNED TO FIELD AND NOT DRAGGED, NEED TO READJUST POSITION
								if(noD.mode!=='scientific' && noD.assign_field && !noD.pos_moved) NightOwl.positionCalc($calc.parent(), $calc.parent().prev($('.'+noD.assign_field)), noD);								
								noD.mode='scientific';
							}});
						// IN SCIENTIFIC MODE
						}else{
							$(this).html('&#x25B2').val($(this).html());
							$sciKeys.stop(true,false).animate({opacity:0},{duration:noD.mode_switch_speed.pF(),queue:false,complete:function(){ 
								$sciKeys.css('display','none'); 
								$keysCont.addClass('no_basic_mode');

								// IF ASSIGNED TO FIELD AND NOT MOVED, NEED TO READJUST POSITION
								if(noD.mode!=='basic' && noD.assign_field && !noD.pos_moved) NightOwl.positionCalc($calc.parent(), $calc.parent().prev($('.'+noD.assign_field)), noD);								
								noD.mode='basic';							
							}});
						};
                                                parent.preTally.Settings.calculatorModeManage(noD.mode);
						e.preventDefault();
					});
			// TOGGLING DISABLED
			}else{
				$this.find('input.no_key_toggle').css({opacity:0.5,cursor:'default'}).addClass('no_toggle_key_inactive')
					.on('click touchstart', function(e){ return false; });
			};
			
			// SETUP INITIAL STATE
			if(noD.start_mode==='basic'){
				var $calc=$this,
					$keysCont=$calc.find('div.no_main_keys'),
					$sciKeys=$keysCont.find('input[type=button]').not('input.key_basic'),
					$toggle=$calc.find('input.no_key_toggle');
				$toggle.html('&#x25B2').val($toggle.html());
				$sciKeys.css({opacity:0,display:'none'}); 
				$keysCont.addClass('no_basic_mode');
				noD.mode='basic';
			};


		/**********************************************************************************/
		// MINI CALCULATORS
		/**********************************************************************************/
		}else{
			// PRESET EQUATIONS
			switch(noD.equation.toLowerCase().removeWS()){
				case 'velocity'     : noD.equation="Velocity[m/s]=Distance[meters] / Time[seconds]"; break;
				case 'acceleration' : noD.equation="Acceleration[m/s]=( Final_Speed[m/s] - Initial_Speed[m/s] ) / Time[seconds]"; break;
				case 'momentum' : noD.equation="Momentum[Ns]=Mass[kg] * Velocity[m/s]"; break;
				case 'bmi': noD.equation="BMI=( Weight[lbs] / ( ( ( Height_Feet[ft] * 12 ) + Height_Inches[inches] ) * ( ( Height_Feet[ft] * 12 ) + Height_Inches[inches] ) ) ) * 703"; break;
				case 'kinetic_enery': noD.equation="Kinetic_Energy[KE]=0.5 * Mass[kg] * Velocity[m/s] ^^ 2"; break;
				case 'potential_energy': noD.equation="Potential_Enery[PE]=Mass[kg] * Acceleration_of_Gravity[m/s^2] * Height[meters]"; break;
				case 'depreciation': noD.equation="Period_Depriciation[$/Years]=( Initial_Cost[$] - Salvage_Value[$] ) / Useful_Years"; break;
				case 'simple_interest': noD.equation="Simple_Interest=Principal[$] * ( Interest_Rate[%] / 100 ) * Number_of_Periods"; break;
				case 'compound_interest' : noD.equation="Amount_Accumulated[$]=Principal[$] * |( 1 + ( ( Interest_Rate[%] / 100 ) / Compoundings_per_Year ) )| ^^ ( Compoundings_per_Year * Number_of_Years )"; break;
				case 'proportion': noD.equation="Proportionate Value=( Value_A * Value_C ) / Value_B"; break;
				case 'ounces_cups' : noD.equation="Cups=Ounces[oz] / 8"; break;
				case 'fahrenheit_celsius': noD.equation="Degrees[C]=( ( Degrees[F] - 32 ) * 5 ) / 9"; break;
				case 'celsius_fahrenheit': noD.equation="Degrees[F]=( ( Degrees[C] * 9 ) / 5 ) + 32"; break;
				case 'force'		: noD.equation="Force[N]=Mass[kg] * Acceleration[(m/s)^2]"; break;
				case 'horsepower'	: noD.equation="Horsepower[Watt]=Torque[pound/ft] * ( Speed[rpm] / 5252)"; break;
				case 'centripetal_force': noD.equation="Centripetal Force[F]=( Mass[kg] * Velocity[m/s]^2 ) / Circular_Radius[meters]"; break;
				case 'discount' : noD.equation="Sale_Price[$]=Cost[$] - ( Cost[$] * ( Discount[%] / 100 ) )"; break;
				case 'markup' : noD.equation="Markup_Price[$]=Trade_Cost[$] + ( Trade_Cost * ( Desired_Markup[%] / 100 ) )"; break;
				case 'area_triangle' : noD.equation="Area_of_Triangle=( Length_of_Base * Length_of_Height ) / 2"; break;				
				case 'project_cost' : noD.equation="Project_Cost[$]=( Time[hours] * Hourly_Rate[$] ) + ( Revisions[#] * Revision_Cost[$] )"; break;
			};
			
			var $calc=$(this),
				equation=noD.equation.toString(),
				leftSide=equation.split('=')[0],
				rightSide=equation.split('=')[1],
				orgWords=rightSide.split(' '),
				words=rightSide.split(' '),
				units='', answer_units='',
				wordArray=[],
				html='<form class="night_owl_mini">';
			
			// LOOP THROUGH CHARACTERS IN EQUATION AND FIND 'WORDS'
			for(var i=0, l=words.length; i<l; i++){
				// IF WORD HAS UNITS
				if(words[i].indexOf('[')>=0){
					var startPos=words[i].indexOf('[')+1,
						endPos=words[i].indexOf(']',startPos),
						units=words[i].substring(startPos,endPos),
						endStr=words[i].substring(endPos+1,words[i].length);
					// REMOVE FROM LABEL
					words[i]=words[i].substring(0,startPos-1)+endStr;
				};

				// DON'T ALLOW CARET IN TITLE
				if(words[i].indexOf('^') > 0){
					var caretPos=words[i].indexOf('^');
					words[i]=words[i].substring(0,caretPos);
				};
				
				// CREATE HTML STRING OF INPUTS (DON'T ALLOW MULTIPLE INPUTS WITH THE SAME NAME)
				if(words[i].match(/^[a-zA-Z\[\]_]*$/) && html.indexOf('class="no_name">'+words[i].replace(/\_/g,' ')+'</label>') <= 0){
					html+='<div class="no_input_wrapper"><label class="no_name">'+words[i].replace(/\_/g,' ')+'</label><label class="no_units">'+units.replace(/\_/g,' ')+'</label><input type="text" class="no_in no_'+words[i]+'"></div>';
				};
			};
			
			// ADD BUTTONS AND ANSWER FIELD
			if(leftSide.indexOf('[')>=0){
				var startPos=leftSide.indexOf('[')+1,
					endPos=leftSide.indexOf(']',startPos),
					answer_units=leftSide.substring(startPos,endPos);
				// REMOVE FROM LABEL
				leftSide=leftSide.substring(0,startPos-1); 
			};

			// ADD HTML 
			var answer_input=noD.show_answer.isB() ? '<div class="no_input_wrapper"><label class="no_name">'+leftSide.replace(/\_/g,' ')+'</label><label class="no_units">'+answer_units.replace(/\_/g,' ')+'</label><input type="text" class="no_out" readonly></div>' : '';
			$calc.html(html+=answer_input+'<input type="button" value="Calculate" class="no_calculate"><input type="button" value="Reset" class="no_reset"></form>')

			// RESET BUTTON
			.find('input.no_reset').on('click touchstart',function(e){
				var noD=$calc.data('nightowl');
				$calc.find('input.no_in').add($calc.find('input.no_out')).val('');
				if(noD.assign_field) $calc.prev($('.'+noD.assign_field)).val('');
				e.preventDefault(); 
			})

			// CALCULATE BUTTON
			.end().find('input.no_calculate').on('click touchstart',function(e){
				var newEQ='', answer=false;
				
				// LOOP THROUGH INPUTS AND CHECK FOR EMPTY OF NON-NUMERIC FIELDS
				for(var i=0, l=$calc.find('input.no_in').length; i<l; i++){
					var $inp=$($calc.find('input.no_in')[i]),
						val=$inp.val();
					if(val=='' || isNaN(val))$inp.val('Invalid');						
				};
				
				// LOOP THROUGH WORDS AND BUILD ANSWER STRING		
				for(var i=0, l=words.length; i<l; i++){
					// ITS A WORD! 
					if(words[i].match(/^[a-zA-Z\[\]_]*$/)){
						// GET VALUE FROM INPUT
						var $input=$calc.find('input.no_'+words[i]),	
							value=parseFloat($input.val(),10);
						
						// EXIT IF INVALID VALUE						
						if(isNaN(value) || $input.val()=='') return;
						
						// ADD VALUE TO NEW EQUATION
						newEQ+=value;
					// ITS NOT A WORD
					}else{ newEQ+=words[i]; };
				};
				
				// AFTER NEW EQUATION STRING IS MADE, DETERMINE IF EXPONENTS ARE USED:
				if(newEQ.indexOf('^') > -1){
					var caretPos=newEQ.indexOf('^');

					// USING EQUATION AS BASE
					if(newEQ.indexOf('^^') > -1){
						// | CHARACTER IS WRAPPED AROUND BASE IN SOME SCENARIOS TO DENOTE OPERATION ORDER
						if(newEQ.indexOf('|') > 0){
							var firstPipe=newEQ.indexOf('|'),
								lastPipe=newEQ.lastIndexOf('|'),
								base=newEQ.substring(firstPipe+1,lastPipe),
								power=newEQ.substring(caretPos+2,newEQ.length),
								// REMOVE CARETS AND PIPES FROM STRING
								factor=newEQ.replace(base,'').replace(power,'').replace(/\|/g, '').replace(/\^/g,''),
								sub_answer=Math.pow(eval(base),eval(power)).toString(),
								answer=eval(factor+sub_answer);							
						}else{
							var base=newEQ.substring(0,caretPos),
								power=newEQ.substring(caretPos+2,newEQ.length),
								answer=Math.pow(eval(base),eval(power));
						};
					};					
					
				// NO EXPONENTS
				}else{ var answer=eval(newEQ); };
				
				// VALIDATE AND INPUT ANSWER
				if(isNaN(answer)){
					$calc.find('input.no_out').val('Error'); 
				}else{ 
					if(noD.assign_field){
						// APPLY ANSWER TO ASSIGNED INPUT
						$calc.prev($('.'+noD.assign_field)).val(answer);						
						if(noD.show_answer.isB()) $calc.find('input.no_out').val(answer);
					}else{
						$calc.find('input.no_out').val(answer);
					};
					
					// ON_SOLVE CALLBACK
					NightOwl.answer=answer;
					if(typeof window[noD.on_solve]==='function') window[noD.on_solve].apply(NightOwl);
				};				

				e.preventDefault();
			})
			
			// KEYDOWN ON MINI CALCULATOR INPUTS SHOULD TRY TO CALCULATE
			.end().find('input.no_in').on('keydown',function(e){
				// PRESSING ENTER OR RETURN
				if(e.keyCode===13){
					$(this).parents('form.night_owl_mini:first').find('input.no_calculate').click();
					e.preventDefault();
				};
			});	
			
			// MINI-CALCULATORS THAT AREN'T ASSIGNED TO AN INPUT NEED TO SHOW THEIR ANSWER
			if(!noD.assign_field) noD.show_answer=true;		
		};
		
		// SETUP FIELD ASSIGNMENTS
		if(noD.assign_field){
			$this.css('display','none').prev($('.'+noD.assign_field)).addClass('no_clipping').val(' ');
			noD.opened=false;

			// SHOW CALCULATOR
			$('.'+noD.assign_field).on('click touchstart focus',function(e){
				var $el=$(this);
				
				// CLOSE OTHER CALCULATORS (TABBING)
				NightOwl.closeCalcs($el);
				
				// OPTIONAL POSITION MATCHING 
				if(noD.match_pos) NightOwl.positionCalc($this, $el, noD);

				// MARK AS ACTIVE CACLULATOR
				$this.addClass('active_calc');
				
				// SHOW CALCULATOR
				if(!noD.opened){
					$this.css({'display':'block'}).stop(true,false)
						.animate({opacity:1},{duration:noD.speed.split(',')[0].pF(),queue:false,complete:function(){
							// FOCUS ON APPROPRIATE INPUT
							var type=noD.type.removeWS().toLowerCase(),
								$input=type==='mini' ? $(this).find('input:first') : $(this).find('input.no_calc_field');
							$(this).css('display','block');
							$input.focus();
							noD.opened=true;
						}});
				};
			});
		};
	});
},



/***********************************************************************************************/
// CLOSE ALL CALCULATORS
/***********************************************************************************************/
closeCalcs:function($target){
	// ALL OPEN CALCULATORS WILL HAVE .ACTIVE_CALC CLASS
	for(var i=0, l=$('.active_calc').length; i<l; i++){
		var $calc=$($('.active_calc')[i]),
			noD=$calc.data('nightowl');
		// CLOSE VISIBLE CALCULATOR IF NOT CLICKING INTO INPUT
		if(noD.opened && $target[0]!==$('.'+noD.assign_field)[0]){
			// IF CLICKING INSIDE THE CALCULATOR
			if($target.parents('.active_calc').length > 0) return; 
				
			noD.opened=false;
			$calc.stop(true,false).animate({opacity:0},{duration:noD.speed.split(',')[1].pF(),queue:false,complete:function(){ 
				$(this).css('display','none'); 
			}});
		};
	};
},


/***********************************************************************************************/
// FORMAT STRING AS MONEY (THOUSANDS, AND DECIMALS)
/***********************************************************************************************/
formatMoney:function(value){ return '$'+value.pF().toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); },

/***********************************************************************************************/
// POSITION CALCULATOR
/***********************************************************************************************/
positionCalc:function($calc, $el, noD){
	// GET POSITION OF ELEMENT
	var mode=noD.pos_mode.toString().toLowerCase().removeWS(),
		el_o=mode==='fixed' ? $el[0].getBoundingClientRect() : $el.offset(),		
		el_x=el_o.left,	
		el_y=el_o.top,
		el_w=$el.outerWidth().pF(),		el_h=$el.outerHeight().pF(),
		calc_w=$calc.outerWidth(),		calc_h=$calc.outerHeight(),
		off=noD.offset.split(',');
		
	switch(noD.match_pos){
		case 'right_down' : var top=el_y, left=el_x+el_w; break;
		case 'right_up'   : var top=el_y-calc_h+el_h, left=el_x+el_w; break;
		case 'right_mid'  : var top=el_y-calc_h/2+el_h, left=el_x+el_w; break;
		case 'top_left'   : var top=el_y-calc_h, left=el_x; break;
		case 'top_right'  : var top=el_y-calc_h, left=el_x-(calc_w-el_w); break;
		case 'top_mid'     : var top=el_y-calc_h, left=el_x-(calc_w/2)+(el_w/2); break;
		case 'bottom_right': var top=el_y+el_h, left=el_x-(calc_w-el_w); break;
		case 'bottom_left' : var top=el_y+el_h, left=el_x; break;
		case 'bottom_mid'  : var top=el_y+el_h, left=el_x-(calc_w/2)+(el_w/2); break;												
		case 'left_up'    : var top=el_y-calc_h+el_h, left=el_x-calc_w; break;
		case 'left_down'  : var top=el_y, left=el_x-calc_w; break;
		case 'left_mid'   : var top=el_y-calc_h/2+el_h, left=el_x-calc_w; break;						
	};
	$calc.css({'position':mode, 'top':top+off[1].pF()+'px', 'left':left+off[0].pF()+'px'});
},

/***********************************************************************************************/
// CALCULATOR MEMORY METHOD
/***********************************************************************************************/
memory:function($calc, operator){
	var $memField=$calc.find('input.no_calc_mem'),
		$calcField=$calc.find('input.no_calc_field');
	$calcField.focus();
	
	// MEMORY SAVE FUNCTION
	if(operator==1){
		$memField.val($calc.find('input.no_results').val());
	// MEMORY RECALL FUNCTION
	}else if(operator==2){
		var mem=$memField.val().removeWS();
		if(mem==0 || NightOwl.chracter(mem.charAt(0))) mem='';
		$calcField[0].value=mem;
	// CLEAR SCREEN
	}else if(operator==3){
		if($calcField.val()==''){  
			$calc.find('input.no_results').val(' ');
		}else{  
			$calcField.val(' '); 
		};	
	};
},


/***********************************************************************************************/
// CALCULATOR DISPLAY METHOD
/***********************************************************************************************/
display:function($calc, xyz){
	if(xyz==''){ $calc.find('input.no_calc_field').focus();
	}else{ $calc.find('input.no_results').select(); };
},

/***********************************************************************************************/
ghij:function($calc, sciKeyNum){
	var answer='', mem=0,
		$result=$calc.find('input.no_results'),
		$calcField=$calc.find('input.no_calc_field');

	// RETURN ON INITIAL BUTTON PRESS TO AVOID SYNTAX ERROR	
	if(!$calcField.val() && !$result.val()) return;

	// SCIENTIFIC KEYS
	if(sciKeyNum>=1){
		if($calcField.val()==''){
			val=$calc.find('input.no_results').val();
		}else{ 
			val=$calcField.val();
			if(NightOwl.resultant(val.charAt(0))) val=$calc.find('input.no_results').val()+val;
		};	
	};
	
	// BUILD STRING
	for(var i=0; i<val.length; i++){ if(val.charAt(i)==','){ answer += '.'; }else{ answer += val.charAt(i); };};
	if(NightOwl.operator(val.charAt(val.length-1))) return false;
	answer=eval('1*'+answer);

	// SCIENTIFIC KEYS
	if(sciKeyNum>1) answer=NightOwl.mathcalc($calc, sciKeyNum, answer);
	
	// IF USING A NEGATIVE NUMBER AND IS NEGATIVE OPERATION, NEED TO EVALUATUE FIRST
	var oldVal=$calc.find('input.no_oldresult')[0].value;
	if(answer<0 && $calcField.val()<0) var answer=eval(oldVal+answer);
	
	// SAVE ANSWER AS OLD ANSWER
	$calc.find('input.no_oldresult').val(answer);

	// DISPLAY RESULT
	NightOwl.result($calc, answer);
	$calcField.val('').focus();
},

/***********************************************************************************************/
// CALCULATOR RESULTS METHOD
/***********************************************************************************************/
result:function($calc, screenVal){ 
	decimal=($calc.find('select.no_dec_select')[0].options[$calc.find('select.no_dec_select')[0].selectedIndex].value).pF();
	var strVal=screenVal+' '; // CONVERT TO STRING
	if(strVal.charAt(0)=='.')strVal='0'+strVal;
	var intVal=strVal.length-1, // REMOVE LAST CHARACTER		
		noD=$calc.parent().data('nightowl');	
	NightOwl.decklmn(strVal);
	
	// DECIMALS
	if(intVal>16 && ghi==-1){
		if(decimal==-1)decimal=14; 
		strVal=NightOwl.xyzab(strVal.substring(0,intVal))+' ';
		intVal=strVal.length-1;
		NightOwl.decklmn(strVal);
	};

	if(decimal>=0 && decimal!=14){
		if(def>0){ 
			var answer=NightOwl.xyzab(strVal.substring(0,intVal));
		}else{
			screenVal=strVal.substring(0,intVal);
			if(decimal>0){ 
				screenVal+='.';
				for(var n=0; n<decimal; n++){ screenVal+='0'; };
			};
			var answer=screenVal;
		};
	}else{ decimal=14; var answer=NightOwl.xyzab(strVal); };
	if(answer.charAt(0)=='.') answer='0'+answer;
	
	// DISPLAY RESULTS WITH/WITHOUT ANIMATION
	if(!noD.result_animation){
		// DISPLAY RESULT
		$calc.find('input.no_results').val(isNaN(answer) ? 'Error' : answer);
	}else{
		// ANIMATE AND DISPLAY RESULT
		$calc.toggleClass('no_calc_ani'+noD.result_animation).find('input.no_results').val(isNaN(answer) ? 'Error' : answer);
	};
	
	// UPDATE ASSIGNED FIELD
	if(noD.assign_field)$calc.parent().prev('.'+noD.assign_field).val(isNaN(answer) ? 'Error' : answer);
	
	// ON_SOLVE CALLBACK
	NightOwl.answer=answer;	
	if(typeof window[noD.on_solve]==='function') window[noD.on_solve].apply(NightOwl);
},	

/***********************************************************************************************/
xyzab:function(data1){
	with(Math){
		if(ghi==-1){
			var value=def;
			if(value==-1){ value=data1.length; };
			var value1='';
			if(value>16){ 
				var value2=round(data1*pow(10,18))+' ',
					value3=value2.indexOf('e'),
					valuek=(value2.substring(0,value3));
				valuek=round(valuek*pow(10, 15))/pow(10, 15)+' ';
				value1=(value2.substring(value3+2,value2.length-1));
				value1='e+'+(value1-18);
			}else{
				var valuek=round(data1*pow(10,decimal))/pow(10, decimal)+' ';
			};
		}else{
			var valuek=data1.substring(0,ghi),
				value1=data1.substring(ghi,data1.length);
			valuek=round(valuek*pow(10, decimal))/pow(10, decimal) + ' ';
		};
		valuek=valuek.substring(0, valuek.length-1);
		if(valuek.charAt(0)=='.'){ valuek='0'+valuek; };
		if(decimal<14){
			if(valuek.indexOf('.')==-1 && decimal!=0){ valuek+='.'; };
			var nula=(def+decimal)-(valuek.length-1);
			if(nula>0 && decimal>0){ for(var n=0; n<nula; n++){ valuek+='0';}; };
		};
	return(valuek+' '+value1);
	};
},

/***********************************************************************************************/	
mathcalc:function($calc, klmn, mno){ 
	with(Math){
		if(klmn==2){ mno=pow(mno,2);
		}else if(klmn==3){ mno=sqrt(mno);
		}else if(klmn==4){ mno=-mno;
		}else if(klmn==5){ mno=log(mno);
		}else if(klmn==6){ mno=pow(E, mno);
		}else if(klmn==7){ mno=1/mno;
		}else if(klmn==8){ mno=log(mno)/LN10;
		}else if(klmn==9){ mno=pow(10,mno);
		}else if(klmn>=10 && klmn<=12){
			if(klmn==10){ mno=atan(mno);
			}else if(klmn==11){ mno=acos(mno);
			}else if(klmn==12){ mno=asin(mno); };
			if($calc.find('input.key_degree')[0].checked) mno=(mno*180)/PI;
		}else if(klmn>=14 && klmn<=16){
   			if($calc.find('input.key_degree')[0].checked){ radian=(mno/180)*PI;
			}else{ radian=mno; };
			if(klmn==14){ mno=tan(radian);
			}else if(klmn==15){ mno=cos(radian);
			}else if(klmn==16){ mno=sin(radian); };
		}else if(klmn==17){ mno=mno/100;
		}else if(klmn==18){ mno=mno/1000000;
		}else if(klmn==20){ mno=NightOwl.factorial(mno);
		}else if(klmn==21){ jkl=prompt("Enter Root", 3); mno=pow(mno, jkl);
		}else if(klmn==22){ jkl=prompt("Enter Root", 3); mno=pow(mno, (1/jkl)); };
		return mno; 
	};
},	

/***********************************************************************************************/
// BACKSPACE KEY
/***********************************************************************************************/
backspace:function($calc){
	var $calcField=$calc.find('input.no_calc_field'), input=$calcField[0].value;
	$calcField[0].value=input.substring(0,input.length-1);},
/***********************************************************************************************/
// CALCULATOR HELPER / UTILITY METHODS
/***********************************************************************************************/
cdef:function($calc, xyz){ $calc.find('input.no_calc_field').focus()[0].value+=xyz; },
factorial:function(n){ if((n==0)||(n==1)){ return 1; }else if(n<0){ return 'Error'; }else{ var opqrst=(n*NightOwl.factorial(n-1)); return opqrst };},
decklmn:function(data1){ def=0; ghi=0; def=data1.indexOf("."); ghi=data1.indexOf("e"); },	
resultant:function(valuer){ var resultant='*/+'; for(var i=0; i<resultant.length; i++)if(valuer==resultant.charAt(i)){ return true }; return false; },
operator:function(valuer){ var dashop='*/+-'; for(var i=0; i<dashop.length; i++) if(valuer==dashop.charAt(i)){ return true }; return false; },	
chracter:function(valuer){ 
	var chracter="(ABCDEFGHIKLMNOPRSTUVWXYZ";
	for(var i=0; i<chracter.length; i++)if(valuer==chracter.charAt(i)){return true}{return false};
},
/***********************************************************************************************/
// BUILD CALCULATOR HTML
/***********************************************************************************************/
buildHTML:function($calc, noD){
	var $solar=noD.solar ? '<div class="no_solar"><div class="no_title">'+noD.title+'<br><span class="no_sub">'+noD.subline+'</span></div><div class="no_solpanels"><span></span><span></span><span></span><span></span></div></div>' : '';
	
	// TYPE OF NIGHTOWL CALCULATOR TO LOAD
	if(noD.type.toString().toLowerCase()==='standard'){
		$calc.css({'display':noD.assign_field!==false ? 'none' : 'block','position':noD.pos_mode.toString().toLowerCase().removeWS()}).html('<form name="night_owl_calc" class="night_owl_calc no_skin'+noD.skin+'">'+$solar+'<input type="hidden" name="no_oldresult" value="" class="no_oldresult"><input type="hidden" name="no_calc_mem" value="" class="no_calc_mem"><input type="text" SIZE="16" name="resultant" readonly="true" class="no_results"><SELECT SIZE="1" hidden class="no_dec_select"><OPTION VALUE="-1" SELECTED>decimal</OPTION></SELECT><input type="text" SIZE="17" name="no_calc_field" class="no_calc_field"><div class="night_owl_rbs"><div class="no_rb"><input type="radio" name="vwxyz" title="Radians" checked class="key_radians"><label for="Radians" class="link_rad">RAD</label><input type="radio" name="vwxyz" title="Degree" class="key_degree" value="degrees"><label for="Degree" class="link_deg">DEG</label><input type="radio" name="vwxyz" title="Gradient" class="key_gradient" value="grads"><label for="Gradient" class="link_grad">GRAD</label></div><div class="top_keys"><input type="button" title="Switch to Basic Mode" name="toggle" readonly="true" class="no_key_toggle no_calc_key" value="&#x25BC"><input type="button" title="Backspace" name="Backspace" value="&larr;" readonly="true" class="key_backspace no_calc_key"><input type="button" name="Cls" value="Cls" title="Clear Screen" class="key_cls no_calc_key key_wide" readonly="true"></div></div><div class="no_main_keys"><input type="button" name="sqrt" value="&#x221A" title="Square Root" class="key_squareroot no_calc_key" readonly="true"><input type="button" name="root" value="root" title="Root" class="key_root no_calc_key" readonly="true"><input type="button" name="ln" value="ln" title="Natural Logarithm" class="key_natlog no_calc_key" readonly="true"><input type="button" name="log" value="log" title="Common Logarithm" class="key_log no_calc_key" readonly="true"><input type="button" name="MS" value="MS" title="Memory Store" class="key_MS no_calc_key" readonly="true"><input type="button" name="MR" value="MR" title="Memory Recall" class="key_MR no_calc_key"><input type="button" name="kvadrat" value="x^2" title="Square" class="key_square no_calc_key" readonly="true"><input type="button" name="potencija" value="x^y" title="Power" class="key_xy no_calc_key" readonly="true"><input type="button" name="aln" value="e^x" title="Natural Antilogarithm" class="key_natanti no_calc_key" readonly="true"><input type="button" name="alog" value="10^x" title="Common Antilogarithm" class="key_antil no_calc_key" align="left" readonly="true"><input type="button" name="lijevo" title="Open Parenthesis" value="(" class="key_parl no_calc_key" readonly="true"><input type="button" name="desno" title="Close Parenthesis" value=")" class="key_parr no_calc_key" readonly="true"><input type="button" name="1/x" title="Reciprical" value="1/x" class="key_1x no_calc_key" readonly="true"><input type="button" name="fact" value="x!" title="Factorial" class="key_x no_calc_key" readonly="true"><input type="button" name="PI" title="Pi" value="&#960;" class="key_pi key_basic" readonly="true"><input type="button" name="sign" value="&#x00B1" title="Change Sign" class="key_changesign key_basic" readonly="true"><input type="button" name="postotak" value="%" title="Percent" class="key_percent key_basic" readonly="true"><input type="button" name="djeljeno" title="Divide" value="&#x00F7" class="key_divide key_basic" readonly="true"><input type="button" name="sin" value="sin" title="Sine" class="key_sin no_calc_key" readonly="true"><input type="button" name="asin" value="asin" title="Arcsine" class="key_asin no_calc_key" readonly="true"><input type="button" name="7" value="7" class="key_7 key_basic" readonly="true"><input type="button" name="8" value="8" class="key_8 key_basic" readonly="true"><input type="button" name="9" value="9" class="key_9 key_basic" readonly="true"><input type="button" name="puta" title="Multiply" value="*" class="key_multiply key_basic" readonly="true"><input type="button" name="cos" value="cos" title="Cosine" class="key_cos no_calc_key" readonly="true"><input type="button" name="acos" value="acos" title="Arccosine" class="key_acos no_calc_key" readonly="true"><input type="button" name="4" value="4" class="key_4 key_basic" readonly="true"><input type="button" name="5" value="5" class="key_5 key_basic" readonly="true"><input type="button" name="6" value="6" class="key_6 key_basic" readonly="true"><input type="button" name="minus" title="Subtract" value="-" class="key_minus key_basic" readonly="true"><input type="button" name="tan" value="tan" title="Tangent" class="key_tan no_calc_key" readonly="true"><input type="button" name="atan" value="atan" title="Arctangent" class="key_atan no_calc_key" readonly="true"><input type="button" name="1" value="1" class="key_1 key_basic" readonly="true"><input type="button" name="2" value="2" class="key_2 key_basic" readonly="true"><input type="button" name="3" value="3" class="key_3 key_basic" readonly="true"><input type="button" name="plus" title="Add" value="+" class="key_add key_basic" readonly="true"><input type="button" name="exp" title="Exponent" value="e" class="key_e no_calc_key" readonly="true"><input type="button" name="ppm" value="ppm" title="Part Per Milion" class="key_ppm no_calc_key" readonly="true"><input type="button" name="0" value="0" class="key_0 key_basic key_wide" readonly="true"><input type="button" name="." title="Decimal" value="." class="key_decimal key_basic" readonly="true"><input type="button" name="enter" title="Equals" class="key_enter key_basic" value="=" readonly="true"></div></FORM>');

	$calc.find('input[type=button]').on('touchstart',function(e){
		e.preventDefault();
		e.stopPropagation();
		
	});
	};
},

/***********************************************************************************************/
// ADD DRAG FUNCTIONALITY TO CALCULATOR
/***********************************************************************************************/
addDrag:function($calc, $handle){
	// FIGURE OUT HANDLE
	var $el=!$handle ? $calc : $handle;
	return $el.addClass('no_draggable').on('mousedown', function(e){
			var $this=$(this),
				noD=$this.data('nightowl'),
				$target=$(e.target);
				
			// DON'T DRAG ON BUTTONS
			if($target.hasClass('no_calculate') || $target.hasClass('no_reset')) return;
			
			// SAVE REFERENCE TO ACTIVE CALCULATOR
			NightOwl.$activeCalc=$el;
				
	        var z_idx=$el.css('z-index'),
    	        drg_h=$el.outerHeight(),
        	    drg_w=$el.outerWidth(),
	            pos_y=$el.offset().top+drg_h-e.pageY,
	    	    pos_x=$el.offset().left+drg_w-e.pageX,
				tI=$el.data('nightowl').topIndex.pF()+1;
	        $el.css('z-index',tI).data('nightowl').topIndex=tI;
			
			$(document).on('mousemove.NightOwl',function(e){
				if(!NightOwl.$activeCalc) return;
				
				// CALCULATOR HAS BEEN DRAGGED (DON'T REPOSITION ON TOGGLE)
				noD.pos_moved=true;
				
				NightOwl.$activeCalc.offset({top:e.pageY+pos_y-drg_h, left:e.pageX+pos_x-drg_w})
				.on('mouseup',function(){ $(this).removeClass('draggable'); });
    	    });

			// DON'T PREVENT DEFAULT ON INPUTS
			if(!$target.hasClass('no_in')) e.preventDefault(); 
			
		}).on('mouseup', function(){
			var $this=$(this);
			NightOwl.$activeCalc=false;
			$this.removeClass('draggable').removeClass('active-handle');
        });
}};
/***********************************************************************************************/
// PLUGIN DEFINITION
/***********************************************************************************************/
$.fn.NightOwl=function(method,options){
	if(NightOwl[method]){ return NightOwl[method].apply(this,Array.prototype.slice.call(arguments,1));
	}else if(typeof method==='object'||!method){ return NightOwl.init.apply(this,arguments);
	}else{ $.error('Method '+method+' does not exist'); }
}})(jQuery);

/* EXTEND NATIVE CLASSES */
String.prototype.removeWS=function(){return this.toString().replace(/\s/g, '');};
String.prototype.pF=function(){return parseFloat(this);};
Number.prototype.pF=function(){return parseFloat(this);};
String.prototype.sP=function(splitter,key){return this.toString().split(splitter)[key];};
String.prototype.isB=function(){return this.toString()=="true"?true:false;};
Boolean.prototype.isB=function(){return (this==true)?true:false;};