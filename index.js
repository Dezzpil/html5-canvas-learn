/**
 * Created by root on 10.02.14.
 */
var canvas = document.getElementById('tutorial');
if (canvas.getContext){
    var ctx = canvas.getContext('2d');
    console.log('ok');
    // drawing code here
} else {
    // canvas-unsupported code here
    console.log('not ok');
}
