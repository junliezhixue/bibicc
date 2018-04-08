<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Document</title>
</head>
<body>
	<div id="main" style="width: 600px;height:400px;"></div>
</body>
<script src="/js/echarts.js"></script>
<script type="text/javascript">
    var myChart = echarts.init(document.getElementById('main'));
    myChart.setOption({
    series : [
        {
            name: '访问来源',
            type: 'pie',
	    roseType: 'angle',
            radius: '55%',
            data:[
                {value:235, name:'视频广告'},
                {value:274, name:'联盟广告'},
                {value:310, name:'邮件营销'},
                {value:335, name:'直接访问'},
                {value:400, name:'搜索引擎'}
            ],
	    itemStyle: {
	emphasis: {
        shadowBlur: 200,
        shadowColor: 'rgba(0, 0, 0, 0.5)'
    },
    // 阴影的大小
    shadowBlur: 200,
    // 阴影水平方向上的偏移
    shadowOffsetX: 0,
    // 阴影垂直方向上的偏移
    shadowOffsetY: 0,
    // 阴影颜色
    shadowColor: 'rgba(0, 0, 0, 0.5)'
}
        }
    ]
})
</script>
</html>
