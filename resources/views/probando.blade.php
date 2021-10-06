<!DOCTYPE html>
            <html>
                <head>
                    <title></title>
                    <style>
                        @page {
                            margin: {{$page_margin_top}}px {{$page_margin_right}}px {{$page_margin_bottom}}px {{$page_margin_left}}px;
                        }
                        header {
                            position: fixed;
                            top: {{$header_top}}px;
                            left: 0;
                            right: 0;
                            background-color: #fff;
                            height: {{$header_height}}px;
                            margin-right: 0;
                            margin-bottom: 55px!important
                        }
                        footer {
                            position: fixed;
                            bottom: {{$footer_bottom}}px;
                            left: 0;
                            right: 0;
                            background-color: #fff;
                            height: {{$footer_height}}px;
                            margin: 0px;
                            padding-top:0px;
                        }
                        main{
                            margin: 0px {{$main_right}}px 0px {{$main_left}}px !important;
                            padding:0;
                        }
                        body{
                            margin: 0;
                            padding-bottom:0px;
                        }
                        #watermark {
                            position: fixed;
                            z-index:  -1000;
                            opacity: 0.1;
                            /* bottom:   0px;
                            left:     0px;
                            width:    15.8cm;
                            height:   28cm;
                            z-index:  -1000; */
                        }
                        body{padding:1em}.codepen body{margin:10px 0 0}.codepen body textarea{display:none}.mce-container textarea{display:inline-block!important}.mce-content-body{font-size:14px;color:#626262;padding:0 25px 25px}.mce-content-body *{background-position:initial}.mce-content-body h1{font-size:34px;font-weight:200;line-height:1.4em;margin:25px 0 15px}.mce-content-body h2{font-size:30px;font-weight:200;line-height:1.4em;margin:25px 0 15px}.mce-content-body h3{font-size:26px;font-weight:200;line-height:1.4em;margin:25px 0 15px}.mce-content-body h4{font-size:22px;font-weight:200;line-height:1.4em;margin:25px 0 15px}.mce-content-body h5{font-size:18px;font-weight:200;line-height:1.4em;margin:25px 0 15px}.mce-content-body h6{font-size:14px;font-weight:200;line-height:1.4em;margin:25px 0 15px}.mce-content-body p{margin:25px 0}.mce-content-body pre{font-family:monospace}.mce-content-body ol,.mce-content-body ul{margin-left:15px;list-style-position:outside;margin-bottom:20px}.mce-content-body ol li,.mce-content-body ul li{margin-left:10px;margin-bottom:10px;color:#626262}.mce-content-body ul{list-style-type:disc}.mce-content-body ol{list-style-type:decimal}.mce-content-body a[href]{text-decoration:underline}.mce-content-body table{width:100%;border-spacing:0;border-collapse:separate;border:1px solid #aaa}.mce-content-body table tr:nth-child(even){background:#fafafa}.mce-content-body table caption,.mce-content-body table td,.mce-content-body table th{padding:15px 7px;font:inherit}.mce-content-body table td,.mce-content-body table th{border:1px solid #aaa;border-collapse:collapse}.mce-content-body table th{font-weight:400;color:#6e6e6e;background-position:100% 100%;background-size:2px 10px;background-repeat:no-repeat}.mce-content-body table th:last-child{background:0 0}.mce-content-body hr{border-top:2px solid #bbb}
                    </style>
                </head>
                <body>

                    <header>
                        <img src="data:image/jpg;base64,{{$cabecera}}" style="width: 100%; height: 100%;" />
                    </header>                    
                    <footer style="position: fixed; z-index: -1000; opacity: 1;">
                        <img src="{{$pie}}" style="width: 100%; height: 100%;" />
                    </footer>

                                        
                    @isset($borrador)
                        @if ($borrador==true)
                            <div id="watermark">
                                <img src="images/borrador.png" height="100%" width="100%" />
                            </div>   
                        @endif
                    @endisset
                    
                    <main class="py-4">
                        @for ($i = 0; $i < 25; $i++)
                            Lorem, ipsum dolor sit amet consectetur adipisicing elit. Officiis earum, mollitia expedita aspernatur vero repellendus harum, rem velit consequatur dolorum placeat obcaecati ratione, inventore officia totam esse voluptatibus ex nemo.                            
                            <br>
                        @endfor
                        {{-- @php
                            print($contenido);
                        @endphp --}}
                    </main>
                    <script type="text/php">
                        if ( isset($pdf) ) {
                            $pdf->page_script('
                                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                                $pdf->text(510, 828, "Página $PAGE_NUM de $PAGE_COUNT", $font, 9);
                            ');
                        }
                    </script>

                </body>
            </html>