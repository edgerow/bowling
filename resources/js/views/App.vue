<template>
  <div id="app">
    <h3>Bowling!</h3>
      <div>
        <score-card :bowlingFrames="bowlingFrames" :rolls="rolls" :test="false" :disabledBtns="disabledBtns" :gameOver="gameOver" v-on:pinBtnClick="test = true"></score-card >
    </div>
  </div>
</template>

<script>
import ScoreCard from '@/js/components/ScoreCard';
export default {
  name: 'app',
  components: {
    ScoreCard
  },
  data() {
    return {
		bowlingFrames: [{id:101,num:1,ball1:null,ball2:null,total:null}, {id:102,num:2,ball1:null,ball2:null,total:null}, {id:103,num:3,ball1:null,ball2:null,total:null}, {id:104,num:4,ball1:null,ball2:null,total:null}, {id:105,num:5,ball1:null,ball2:null,total:null}, {id:106,num:6,ball1:null,ball2:null,total:null}, {id:107,num:7,ball1:null,ball2:null,total:null}, {id:108,num:8,ball1:null,ball2:null,total:null}, {id:109,num:9,ball1:null,ball2:null,total:null}, {id:110,num:10,ball1:null,ball2:null,ball3:null,total:null}],
		rolls: [],
		disabledBtns: [],
		gameOver: []
    };
  }
};
</script>
<style scoped>
#app {
	font-family: Roboto;
	margin: 20px;
}
h3 {
  margin: 10px;
}
</style>