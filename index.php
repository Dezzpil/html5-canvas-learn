<html>
<head>
    <title>Canvas tutorial</title>
    <script type="text/javascript">

        /**
         * @throws Error
         * @throws TypeError
         * @returns {CanvasRenderingContext2D}
         */
        function get_canvas_context() {
            var canvas = document.getElementById('tutorial'),
                    ctx = null;

            if ( ! canvas) {
                throw new Error('No <canvas /> element found');
            }

            try {
                ctx = canvas.getContext('2d');
            } catch ($e) {
                throw new TypeError('Canvas doesnt support by browser');
            }

            return canvas.getContext('2d');
        }

        /**
         *
         * @returns {{width: (*|Number|number|string|CSSStyleDeclaration.width), height: (*|Number|number|string|CSSStyleDeclaration.height)}}
         */
        function get_canvas_size() {
            var canvas = document.getElementById('tutorial');
            return { 'width' : canvas.width, 'height' : canvas.height };
        }

        /**
         * Задача 1:
         * вращающиеся относительно оси круги
         */

        function create_figure(color, type, pos, method) {

            method = method ? method : 'stroke';
            type = type ? type : null;
            pos = pos ? pos : { x : 0, y : 0, rad : 30 };
            color = color ? color : '#000';

            return {
                'color' : color,
                'type' : type,
                'method' : method,
                'pos' : pos
            }
        }

        /**
         *
         * @param num {int}
         * @param rad {int}
         * @returns {Array}
         */
        function create_figures(num, rad) {

            var path_list = [], i = 0, path;
            for (i; i < num; i++) {

                path = {
                    'color' : '#000000', // rgb
                    'type' : 'message', // figure type
                    'method' : 'fill', // fill or stroke
                    'id' : i,
                    'pos' : {
                        'x' : 0,
                        'y' : 0,
                        'rad' : rad
                    }
                };

                path_list.push(path);
            }

            return path_list;
        }


        /**
         * Получить рандомный цвет в виде 'f0f0f0'
         * @return {String}
         */
        function get_random_color() {
            var hex = Math.round(0xffffff * Math.random());
            return hex.toString(16);
        }

        /**
         *
         */
        function randomize_figures(num) {

            var list = [], i = 0;

            for (i = 0; i < num; i++) {
                list[i] = create_figure();
                list[i].color = '#' + get_random_color();

                if (i % 2 == 0) {
                    list[i].method = 'fill';
                }

                if (i % 3 == 0) {
                    list[i].type = 'heart';
                } else if (i % 5 == 0) {
                    list[i].type = 'message';
                } else {
                    list[i].pos['rad'] = 20;
                }
            }

            return list;
        }

        /**
         *
         * @param count {int}
         * @param xc {int}
         * @param yc {int}
         * @param radc {int}
         * @param offset {int}
         * @returns {Array}
         */
        function define_figures_positions(count, xc, yc, radc, offset) {

            var alpha,
                full_angle = 360,
                step = full_angle / count,
                positions_list = [];

            for (alpha = offset; alpha < full_angle + offset; alpha += step) {

                positions_list.push({
                    x : Math.round(xc + radc * Math.cos(alpha * Math.PI / (full_angle / 2))),
                    y : Math.round(yc + radc * Math.sin(alpha * Math.PI / (full_angle / 2)))
                });
            }

            return positions_list;
        }

        /**
         *
         * @param ctx {CanvasRenderingContext2D}
         * @param path_list {Array}
         * @param position_list {Array}
         * @param draw_fn {Function} Функция, получающая на вход {CanvasRenderingContext2D} и объект фигуры
         */
        function draw_figures(ctx, path_list, position_list) {

            var i = 0;

            for (i; i < path_list.length; i++) {

                ctx.beginPath();

                if ('type' in path_list[i] && path_list[i].type && path_list[i].type in figure_type_list) {

                    // if path have a specific type from figure_type_list
                    figure_type_list[path_list[i].type](ctx, position_list[i].x, position_list[i].y);

                } else {

                    // draw simple circle
                    ctx.arc(position_list[i].x, position_list[i].y, path_list[i].pos['rad'], 0, Math.PI * 2, false);

                }

                ctx.fillStyle = path_list[i].color;

                switch (path_list[i].type) {
                    case 'stroke' : ctx.stroke(); break;
                    case 'fill' : default : ctx.fill();
                }

                ctx.closePath();
            }
        }


        /**
         * Задача 2:
         * отрисовывать любую указанную фигуру, если указана пользователем
         * если нет - рисует круг по умолчанию
         */

        /**
         * Задача 3:
         * прочитать об остальных возможностях canvas
         * продумать несложную игру по дороге домой
         */

       var figure_type_list = {
            'message' : function(ctx, x, y) {
                ctx.moveTo(x, y);
                ctx.quadraticCurveTo(x - 50, y, x - 50, y + 37.5);
                ctx.quadraticCurveTo(x - 50, y + 75, x - 25, y + 75);
                ctx.quadraticCurveTo(x - 25, y + 85, x - 45, y + 85);
                ctx.quadraticCurveTo(x - 15, y + 95, x - 10, y + 75);
                ctx.quadraticCurveTo(x + 50, y + 75, x + 50, y + 37.5);
                ctx.quadraticCurveTo(x + 50, y, x, y);
            },
            'heart' : function(ctx, x, y) {
                ctx.moveTo(x, y);
                ctx.bezierCurveTo(x, y-3, x-5, y-15, x-25, y-20);
                ctx.bezierCurveTo(x-55, y-15, x-55, y+22.5, x-55, y+22.5);
                ctx.bezierCurveTo(x-55, y+40, x-35, y+62, x, y+60);
                ctx.bezierCurveTo(x+35, y+62, x+55, y+40, x+55, y+22.5);
                ctx.bezierCurveTo(x+55, y+22.5, x+55, y-15, x+25, y-15);
                ctx.bezierCurveTo(x+10, y-15, x, y-3, x, y);
            }
        }


        /**
         *
         */
        function main() {

            var ctx = get_canvas_context(),
                i = 0, timer,
                circles = randomize_figures(10),
                positions = [];

            timer = setInterval(function() {
                positions = define_figures_positions(circles.length, 150, 150, 100, i++);
                ctx.clearRect(0, 0, 400, 500);
                draw_figures(ctx, circles, positions);
            }, 10);

        }

    </script>

</head>
<body onload="main()">
<canvas id="tutorial" width="400" height="500"></canvas>
</body>
</html>