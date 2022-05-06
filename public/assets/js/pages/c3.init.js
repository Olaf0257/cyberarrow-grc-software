!function(t){"use strict";var e=function(){};e.prototype.init=function(){
    c3.generate({
        bindto:"#pie-chart",
        data:{
            columns:[["Lorem1",60],["Lorem2",30],["Lorem3",10]],type:"pie"
        },
        color:{pattern:["#b2dd4c","#E2EBF0","#FD7E15"]},
        pie:{label:{show:!1}}
    })
},t.ChartC3=new e,t.ChartC3.Constructor=e}(window.jQuery),function(t){"use strict";window.jQuery.ChartC3.init()}();