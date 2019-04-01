<template>
	<div class="frame">
		<div class="header">{{frame.num}}</div>
		<div v-if="frame.num != 10" class="ball1">{{frame.ball1}}</div>    
		<div v-if="frame.num != 10" class="ball2">{{frame.ball2}}</div>   
		<div v-if="frame.num == 10" class="ball1-33">{{frame.ball1}}</div>   
		<div v-if="frame.num == 10" class="ball2-33">{{frame.ball2}}</div>   
		<div v-if="frame.num == 10" class="ball3-33">{{frame.ball3}}</div>
		<div class="total">{{frame.total}}</div>
	</div>
</template>

<script type="text/javascript">
  export default {
    props: ['frame'],
    data() {
      return {
        isEditing: false,
      };
    },
    methods:{
    	getID: function(id, num) {
    		return 'frame-' + id + '-' + num;
    	}
    }
  };
</script>
<style scoped>
.frame {
	display: inline-block;
	vertical-align: top;
	border: solid black 2px;
	width: 100px;
	height: 90px;
}
.header {
	border: solid black 1px;
	text-align: center;
	font-size: 18px;
	font-weight: bold;
}
.ball1, .ball1-33, .ball2, .ball2-33, .ball3-33 {
	display: inline-block;
	width: 46px;
	height: 30px;
	vertical-align: top;
	line-height: 30px;
	text-align: center;
	text-align: center;
	font-size: 12px;
}
.ball2, .ball2-33, .ball3-33 {
	border: solid black 1px;
}
.ball1-33, .ball2-33, .ball3-33 {
	width: 29px;
}	
.total {
	width: 100px;
	height: 40px;
	vertical-align: top;
	line-height: 40px;
	text-align: center;
	font-size: 18px;
}
</style>