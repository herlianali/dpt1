/*! Created By: Hangs Breaker */
document.getElementsByTagName("head")[0].insertAdjacentHTML('beforeend','<style>.calc{vertical-align:top;display:block;min-height:34px;padding: 4px 12px 2px 15px;font-size:14px;line-height: 1.42857;color: #555;background-color: #FFF;background-image: none;border: 1px solid #CCC;border-radius: 4px;box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset;transition: border-color 0.15s ease-in-out 0s, box-shadow 0.15s ease-in-out 0s;margin-left:8px;text-align:right;}.calcc{position:absolute;margin-left:8px;background:#EEEEEE;border:1px solid #ccc;border-radius:5px;min-width:9px;min-height:5px;display:block;padding:0px 2px 0px 2px;font-family:Arial;color:#555}</style>');

var enKey = false;
var calc = document.querySelectorAll('.calc');
var forEach = [].forEach;
forEach.call(calc, function (el, i) {
    //calc[i].setAttribute("onblur", "calcs()");
    //calc[i].setAttribute("onclick", "getcalcs()");
    //calc[i].setAttribute("onkeypress", "handle(event)");
    calc[i].insertAdjacentHTML('beforeBegin','<div class="calcc">c</div>');
    calc[i].insertAdjacentHTML('afterEnd','<input type="hidden" class="calcr" style="display:none;"/>');
    var calcr = document.querySelectorAll('.calcr');
    
    function doCalc(){
        calcr[i].value = calc[i].value;
        if(calc[i].value != ''){
            if(calc[i].getAttribute("data")){
                var data = calc[i].getAttribute("data").split("-");
                if(data[0]==="N"){
                    calc[i].value = eval(calc[i].value.replace(/\,/g, ""))===undefined?'':eval(calc[i].value.replace(/\,/g, ""));
                    if(data[1] != null && data[1] != ""){calc[i].value = parseFloat(calc[i].value).toFixed(data[1]);}
                    calc[i].value = calc[i].value.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
                }else if(data[0]==="S"){
                    calc[i].value = eval(calc[i].value.replace(/\./g, "").replace(/\,/g, "."))===undefined?'':eval(calc[i].value.replace(/\./g, "").replace(/\,/g, "."));
                    if(data[1] != null && data[1] != ""){calc[i].value = parseFloat(calc[i].value).toFixed(data[1]);}
                    calc[i].value = calc[i].value.toString().replace(/\./g, ",").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                }
            }else{
                calc[i].value = eval(calc[i].value)===undefined?'':eval(calc[i].value);
            }
        }
    }
    function mouseDown(){
        if(calcr[i].value != ''){
            calc[i].value = calcr[i].value;enKey = false;
        }
    }
    function onBlur(){
        if(enKey){enKey = false;}else{doCalc();}
    }
    
    el.addEventListener('mousedown',mouseDown);
    el.addEventListener('blur',onBlur);
    el.addEventListener('keypress',function(e){
    var key = e.which || e.keyCode;
        if(key === 13){
            enKey = true;
            doCalc();
        }
    });
});
// Label for copy
var calcc = document.querySelectorAll('.calcc');
var forEach = [].forEach;
forEach.call(calcc, function (el, i) {
    function mouseEnter(){
        calcc[i].innerHTML = calc[i].value;
        calcc[i].style.padding = "6px 6px 6px 5px";
    }
    function mouseOut(){
        calcc[i].innerHTML = "c";
        calcc[i].style.padding = "0px 2px 0px 2px";
    }
el.addEventListener('mouseenter',mouseEnter);
el.addEventListener('mouseout',mouseOut);
});
