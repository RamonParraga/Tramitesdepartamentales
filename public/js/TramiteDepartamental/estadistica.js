    
    // color: [
    //     '#26B99A', '#34495E', '#BDC3C7', '#3498DB',
    //     '#9B59B6', '#8abb6f', '#759c6a', '#bfd3b7'
    // ]
    
    //variable para las graficas
    var theme = {
        color: [
            '#0e68a7', '#d10609', '#ff7f12', '#179517',
            '#980597', '#68a90b', '#04787b', '#16a3ef'
        ],


        title: {
            itemGap: 8,
            textStyle: {
                fontWeight: 'normal',
                color: '#408829'
            }
        },

        dataRange: {
            color: ['#1f610a', '#97b58d']
        },

        toolbox: {
            color: ['#408829', '#408829', '#408829', '#408829']
        },

        tooltip: {
            backgroundColor: 'rgba(0,0,0,0.5)',
            axisPointer: {
                type: 'line',
                lineStyle: {
                    color: '#408829',
                    type: 'dashed'
                },
                crossStyle: {
                    color: '#408829'
                },
                shadowStyle: {
                    color: 'rgba(200,200,200,0.3)'
                }
            }
        },

        dataZoom: {
            dataBackgroundColor: '#eee',
            fillerColor: 'rgba(64,136,41,0.2)',
            handleColor: '#408829'
        },
        grid: {
            borderWidth: 0
        },

        categoryAxis: {
            axisLine: {
                lineStyle: {
                    color: '#408829'
                }
            },
            splitLine: {
                lineStyle: {
                    color: ['#eee']
                }
            }
        },

        valueAxis: {
            axisLine: {
                lineStyle: {
                    color: '#408829'
                }
            },
            splitArea: {
                show: true,
                areaStyle: {
                    color: ['rgba(250,250,250,0.1)', 'rgba(200,200,200,0.1)']
                }
            },
            splitLine: {
                lineStyle: {
                    color: ['#eee']
                }
            }
        },
        timeline: {
            lineStyle: {
                color: '#408829'
            },
            controlStyle: {
                normal: {color: '#408829'},
                emphasis: {color: '#408829'}
            }
        },

        k: {
            itemStyle: {
                normal: {
                    color: '#68a54a',
                    color0: '#a9cba2',
                    lineStyle: {
                        width: 1,
                        color: '#408829',
                        color0: '#86b379'
                    }
                }
            }
        },
        map: {
            itemStyle: {
                normal: {
                    areaStyle: {
                        color: '#ddd'
                    },
                    label: {
                        textStyle: {
                            color: '#c12e34'
                        }
                    }
                },
                emphasis: {
                    areaStyle: {
                        color: '#99d2dd'
                    },
                    label: {
                        textStyle: {
                            color: '#c12e34'
                        }
                    }
                }
            }
        },
        force: {
            itemStyle: {
                normal: {
                    linkStyle: {
                        strokeColor: '#408829'
                    }
                }
            }
        },
        chord: {
            padding: 4,
            itemStyle: {
                normal: {
                    lineStyle: {
                        width: 1,
                        color: 'rgba(128, 128, 128, 0.5)'
                    },
                    chordStyle: {
                        lineStyle: {
                            width: 1,
                            color: 'rgba(128, 128, 128, 0.5)'
                        }
                    }
                },
                emphasis: {
                    lineStyle: {
                        width: 1,
                        color: 'rgba(128, 128, 128, 0.5)'
                    },
                    chordStyle: {
                        lineStyle: {
                            width: 1,
                            color: 'rgba(128, 128, 128, 0.5)'
                        }
                    }
                }
            }
        },
        gauge: {
            startAngle: 225,
            endAngle: -45,
            axisLine: {
                show: true,
                lineStyle: {
                    color: [[0.2, '#86b379'], [0.8, '#68a54a'], [1, '#408829']],
                    width: 8
                }
            },
            axisTick: {
                splitNumber: 10,
                length: 12,
                lineStyle: {
                    color: 'auto'
                }
            },
            axisLabel: {
                textStyle: {
                    color: 'auto'
                }
            },
            splitLine: {
                length: 18,
                lineStyle: {
                    color: 'auto'
                }
            },
            pointer: {
                length: '90%',
                color: 'auto'
            },
            title: {
                textStyle: {
                    color: '#333'
                }
            },
            detail: {
                textStyle: {
                    color: 'auto'
                }
            }
        },
        textStyle: {
            fontFamily: 'Arial, Verdana, sans-serif'
        }
    };


    //cargar graficos al inicio
    $(document).ready(function () {
        var listTiemMedioTram =  $('#input_listTiemMedioTram').data("field-id");
        cargar_tiempo_medio_por_tramite(listTiemMedioTram, "D");
        cargar_tramite_generado(listTiemMedioTram);
    });

    //

    // funciona para cargar el grafico de tiempo medio de trámites
    function cargar_tiempo_medio_por_tramite(listTiemMedioTram,tipo_muestra){

        var echartBar = echarts.init(document.getElementById('grafica_promedio_atencion'), theme);

        var data_category = [];
        var data_value = [];
        var tipo = "días";
        if(tipo_muestra=="H"){ tipo="horas"; }

        //verficamos el tipo de muestra

        //creamos la data para enviarla al grafico
        listTiemMedioTram.forEach(dato_estadistico => {

            data_category.push(dato_estadistico.usuario);
            if(tipo_muestra=="D"){ // muestra en dias
                data_value.push(dato_estadistico.tiempo_medio_tramite);
            }else{ // muestra en horas
                data_value.push(dato_estadistico.hora_medio_tramite);
            }
            
        });

        //cargamos la grafica
        echartBar.setOption({
        title: {
            text: 'Tiempo promedio por trámite',
            subtext: 'Tiempo promedio'
        },
        tooltip: { trigger: 'axis' },
        legend: { x: 'center', y: 'bottom',  data: [`Tiempo (${tipo})`] },
        toolbox: { show: true },
        calculable: false,
        xAxis: [{ type: 'category', data: data_category }],
        yAxis: [{ type: 'value' }],
        series: [{
            name: `Tiempo (${tipo})`,
            type: 'bar',
            data: data_value,
            markPoint: { data: [{ type: 'max', name: '???' }, { type: 'min', name: '???' }] },
            markLine: { data: [{ type: 'average', name: '???' }] }
        }]
        });  
        console.warn("Grafico actualizado");      
    }

    // funcion para cargar el grafico de cantidad de tramites generados
    function cargar_tramite_generado(listTiemMedioTram){

        var echartPie = echarts.init(document.getElementById('grafica_distribucion_atencion'), theme);

        var dataGrafica = [];

        //creamos la data para enviarla al grafico
        listTiemMedioTram.forEach(dato_estadistico => {
            dataGrafica.push({
                value: dato_estadistico.cantidad_tramites,
                name: dato_estadistico.usuario
            });
        });
        
        //cargamos la grafica
        echartPie.setOption({
            tooltip: { trigger: 'item', formatter: "{a} <br/>{b} : {c} ({d}%)" },
            legend: { x: 'center', y: 'top', data: ['Izquierda', 'Derecha'] },
            calculable: true,
            series: [{
                name: 'Distribución de Atención de Procesos',
                type: 'pie',
                radius: '55%',
                center: ['50%', '48%'],
                data: dataGrafica
            }]
        });

    } 

    $('.tipomuestra').on('ifChecked', function(event){
        filtrarEstadistica();
    });

    function filtrarEstadistica(){
        var us001 = $("#cmb_usuarios").val();
        var fechaInicio = $("#est_fechaInicio").val();
        var fechaFin = $("#est_fechaFin").val();
        var tipo_muestra = $(".tipomuestra:checked").val();

        if(fechaInicio == ""){ fechaInicio=0; }
        if(fechaFin == ""){ fechaFin=0; }

        $.get(`/estadistica/filtrarEstadisticas/${us001}/${fechaInicio}/${fechaFin}`, function(retorno){
            console.log(retorno);
            cargar_tiempo_medio_por_tramite(retorno.resultado, tipo_muestra);
        });
    }



