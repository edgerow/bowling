<template>
<div>
	<p v-if="!gameOverArr[0]">Select number of pins knocked over</p>
	<button id="btn1" type="button" class="btn" :disabled='disabledBtnArr.includes(1)' title="1 pin knocked over" @click="pinBtnClick(1, rollArr, frameArr, disabledBtnArr, gameOverArr)" value="1">1</button>
	<button id="btn2" type="button" class="btn" :disabled='disabledBtnArr.includes(2)' title="2 pins knocked over" @click="pinBtnClick(2, rollArr, frameArr, disabledBtnArr, gameOverArr)" value="2">2</button>
	<button id="btn3" type="button" class="btn" :disabled='disabledBtnArr.includes(3)' title="3 pins knocked over" @click="pinBtnClick(3, rollArr, frameArr, disabledBtnArr, gameOverArr)" value="3">3</button>
	<button id="btn4" type="button" class="btn" :disabled='disabledBtnArr.includes(4)' title="4 pins knocked over" @click="pinBtnClick(4, rollArr, frameArr, disabledBtnArr, gameOverArr)" value="4">4</button>
	<button id="btn5" type="button" class="btn" :disabled='disabledBtnArr.includes(5)' title="5 pins knocked over" @click="pinBtnClick(5, rollArr, frameArr, disabledBtnArr, gameOverArr)" value="5">5</button>
	<button id="btn6" type="button" class="btn" :disabled='disabledBtnArr.includes(6)' title="6 pins knocked over" @click="pinBtnClick(6, rollArr, frameArr, disabledBtnArr, gameOverArr)" value="6">6</button>
	<button id="btn7" type="button" class="btn" :disabled='disabledBtnArr.includes(7)' title="7 pins knocked over" @click="pinBtnClick(7, rollArr, frameArr, disabledBtnArr, gameOverArr)" value="7">7</button>
	<button id="btn8" type="button" class="btn" :disabled='disabledBtnArr.includes(8)' title="8 pins knocked over" @click="pinBtnClick(8, rollArr, frameArr, disabledBtnArr, gameOverArr)" value="8">8</button>
	<button id="btn9" type="button" class="btn" :disabled='disabledBtnArr.includes(9)' title="9 pins knocked over" @click="pinBtnClick(9, rollArr, frameArr, disabledBtnArr, gameOverArr)" value="9">9</button>
	<button id="btn10" type="button" class="btn" :disabled='disabledBtnArr.includes(10)' title="10 pins knocked over" @click="pinBtnClick(10, rollArr, frameArr, disabledBtnArr, gameOverArr)" value="10">10</button>
	<div class="game-over-box" v-if="gameOverArr[0]"><p v-if="gameOverArr[0]">GAME OVER</p><a href="/" id="new-game-btn" v-if="gameOverArr[0]">Play again</a></div>
	<div class="final-score-box">
		<div class="header">Final Score</div>   
		<div class="total" v-if="gameOverArr[0]">{{frameArr[9].total}}</div>
	</div>
	<br />
    <p class="frame">Frames</p>
    <frame v-for="frame in frameArr" :key="frame.id" :frame.sync="frame" v-bind="frame"></frame>
  </div>
</template>

<script type = "text/javascript" >
import Frame from '@/js/components/Frame';
export default {
	props: ['bowlingFrames', 'rolls', 'disabledBtns', 'gameOver', 'test'],
	components: {
		Frame,
	},
	data: function () {
		return {
			rollArr: this.rolls,
			frameArr: this.bowlingFrames,
			disabledBtnArr: this.disabledBtns,
			gameOverArr: this.gameOver,
			test: this.test
		}
	},	
	methods: {
		pinBtnClick(pins, rollArr, frameArr, disabledBtnArr, gameOverArr) {
			let lastActiveBtn = 10 - pins;
			// if this was a second roll (or a strike), enable all pin buttons
			if (disabledBtnArr.length > 0 || pins == 10) {
				disabledBtnArr.splice(0, disabledBtnArr.length);
			}			
			// else disable relevant pin buttons
			else {
				for (let i = lastActiveBtn + 1; i <= 10; i++) {
					disabledBtnArr.push(i);
				}
			}						
			axios.post('/api/score', {
                rolls: rollArr,
				pins: pins
            })
			.then(function (response) {
				for (let i = rollArr.length; i < response.data.rolls.length; i++) {
					rollArr.push(response.data.rolls[i]);
				}
				// empty frame array and refill with updated data
				frameArr.splice(0, frameArr.length);
				let rollCount = 0;
				for (let i = 0; i < 10; i++) {
					let firstRoll = null;
					let secondRoll = null;
					let total = null;					
					if (response.data.rolls[rollCount] !== undefined) {
						firstRoll = response.data.rolls[rollCount];
					}
					if (response.data.rolls[rollCount + 1] !== undefined) {
						secondRoll = response.data.rolls[rollCount + 1];
					}
					if (firstRoll == 10) {
						firstRoll = 'X';
					}
					if (firstRoll == 10 && rollCount < 20) {
						secondRoll = '';
					}
					if (secondRoll == 10) {
						secondRoll = 'X';
					}
					else if (firstRoll + secondRoll == 10) {
						secondRoll = '/';
					}
					if (firstRoll == 0) {
						firstRoll = '-';
					}
					if (secondRoll === 0) {
						secondRoll = '-';
					}
					if (response.data.frames[i] !== undefined) {
						total = response.data.frames[i];
					}
					let frameObj = {
						id: 101 + i,
						num: i + 1,
						ball1: firstRoll,
						ball2: secondRoll,
						total: total
					}
					if (i == 9) {		
						if (response.data.rolls[20] !== undefined) {
							frameObj.ball3 = response.data.rolls[20];
						}			
						if (frameObj.ball3 != undefined && frameObj.ball3 == 10) {
							frameObj.ball3 = 'X';
						}
						else if (frameObj.ball3 != undefined && frameObj.ball2 + frameObj.ball3 == 10) {
							frameObj.ball3 = '/';
						}
						else if (frameObj.ball3 != undefined && frameObj.ball3 == 0) {
							frameObj.ball3 = '-';
						}
					}
					rollCount += 2;
					frameArr.push(frameObj);
				}
				if (response.data.game_over == true) {
					for (let i = 0; i <= 10; i++) {
						disabledBtnArr.push(i);
					}
					gameOverArr.push(true);
				}
			})
			.catch(function (error) {
				console.log("error: ", error);
			});
		},
		getID: function(index) {
			let count = index + 1;
			return 'frame-' + count;
		}
	}
};
</script>

<style scoped>
p {
  margin: 10px;
}
.btn {
	width: 50px;
	padding: 5px;
	border: 2px;
	margin: 5px;
	font-size: 15px;
	border-radius: 3px;
	background-color: #cecece;
	display: inline-block;
}
.btn:disabled {
	display: none;
}
.game-over-box {
	display: inline-block;
	margin-left: 10px;
	vertical-align: top;
	border: solid black 2px;
	width: 180px;
	height: 60px;
	line-height: 12px;
	text-align: center;
	font-weight: bold;
	font-size: 18px;
}
#new-game-btn {
	vertical-align: top;
	font-weight: bold;
	font-size: 12px;
}
#new-game-btn {
	text-decoration: none;
	color: #0c0;
}
#new-game-btn:hover {
	text-decoration: underline;
}
.final-score-box {
	float: right;
	margin-right: 40px;
	vertical-align: top;
	border: solid black 2px;
	width: 120px;
	height: 90px;
}
.header {
	border: solid gray 1px;
	text-align: center;
	font-size: 18px;
	font-weight: bold;
	line-height: 30px;
}
.total {
	width: 120px;
	height: 80px;
	vertical-align: top;
	line-height: 40px;
	text-align: center;
	font-size: 18px;
}
</style>