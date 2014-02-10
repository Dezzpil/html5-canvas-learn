<html>
<head>
    <title>Canvas tutorial</title>
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script type="text/javascript">

        $(document).ready(function(){

            $.ajax({
                url: 'ral-json/colors.json',
                async: true,
                dataType: 'json',
                success: function (response) {
                    ral = response;

                    var code, group_name,
                        html_options = '',
                        $select = $('#js-ral-groups'),
                        $colors_info = $('#js-colors-list');

                    // show options in select
                    for (code in color_groups) {
                        group_name = color_groups[code];
                        html_options += '<option value=' + code + '>' + group_name + '</option>';
                    }

                    $select.html(html_options).on('change', function() {
                        var chosen_group = parseInt($(this).val()),
                            colors = [], code;

                        for (code in ral) {
                            if (parseInt(code) >= chosen_group && parseInt(code) < (chosen_group + 1000)) {
                                colors.push(ral[code]);
                            }
                        }

                        try {
                            clearInterval(existed_interval);
                        } catch (e) {
                            //
                        }

                        start_circles(30, colors, 80, function(circles, timer) {

                            var html = '', i = 0;
                            for (i; i < circles.length; i++) {
                                html += '<div class="ral-item"><b style="width:16px;height:16px;float:left;margin-right:8px;background-color:' + circles[i].color + ';"></b><p>RGB HEX: '+ circles[i].color +'</p></div>';
                            }

                            existed_interval = timer;
                            $colors_info.html(html);


                        });

                    });

                }
            });


        });

        var drawed_circles_offset = 0,
            existed_interval = null,
            ral = {},
            color_groups = {
                '1000' : 'Желтая',
                '2000' : 'Оранжевая',
                '3000' : 'Красная',
                '4000' : 'Фиолетовая',
                '5000' : 'Синяя',
                '6000' : 'Зеленая',
                '7000' : 'Серая',
                '8000' : 'Коричневая',
                '9000' : 'Белая'
            };


        function start_circles(speed, colors, distance, callback) {

            var ctx = get_canvas_context(),
                circles = randomize_figures(10, colors),
                positions = [],
                figure_type_list = {
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
                },
                timer = setInterval(function() {
                    positions = define_figures_positions(circles.length, 180, 180, distance, drawed_circles_offset++);
                    ctx.clearRect(0, 0, 400, 400);
                    draw_figures(ctx, circles, positions, figure_type_list);
                }, speed);

                callback(circles, timer)
        }


        /**
         * 1. Возможность сочетать цвета в рамках одной группы из ral
         * 2. Возможность управлять:
         *  группой рал (определять множество цветов) (предустановленные варианты, возможность собрать собственную палитру)
         *  скоростью вращения
         *  радиусом
         *  размером кругов
         *  количеством кругов
         *  цветом фона
         * 3, возможность сохранять настройки в виде ссылки
         * 4. возможность инициализировать настройки по ссылке
         */



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
         *
         * @param color
         * @param type
         * @param pos
         * @param method
         * @returns {{color: null, type: null, method: null, pos: null}}
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
        function randomize_figures(num, colors) {

            var list = [], i = 0,
                coef = [0.5, 0.6, 0.7, 0.8, 0.9, 1, 1, 1, 1, 1, 0.9],
                color_index = 0;

            console.log(colors);

            for (i = 0; i < num; i++) {
                list[i] = create_figure();
                //list[i].color = '#' + get_random_color();
                color_index = Math.round(Math.random() * (colors.length - 1));
                console.log(colors[color_index])
                list[i].color = colors[color_index]['rgb_hex'];

                if (i % 2 == 0) {
                    list[i].method = 'fill';
                }

                list[i].pos['rad'] = coef[Math.round(Math.random() * 10)] * 25;
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
        function draw_figures(ctx, path_list, position_list, figure_type_list) {

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


    </script>

</head>
<body>
<canvas id="tutorial" style="float: left;" width="400" height="400"></canvas>
<label for="js-ral-groups">Группа цветов:</label>
<select id="js-ral-groups" style="margin-bottom: 10px;"></select>
<div id="js-colors-list"></div>
</body>
</html>