<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ScoreController extends Controller {
	public $game_info = array();
	public $score = 0;
	public $game_over = false;
	public $rolls = array();
	public $frames = array();

	public function checkForStrike($roll_index) {
		if (isset($this->rolls[$roll_index])) {
			return $this->rolls[$roll_index] == 10;
		}
		return false;
	}
	
	public function extraScoreForStrike($roll_index) {
		// check if next roll is a strike
		if (isset($this->rolls[$roll_index + 2]) && $this->rolls[$roll_index + 2] == 10 && isset($this->rolls[$roll_index + 4]) && $roll_index != 16) {
			return $this->rolls[$roll_index + 2] + $this->rolls[$roll_index + 4];
		}
		else if (isset($this->rolls[$roll_index + 2]) && isset($this->rolls[$roll_index + 3])) {		
			return $this->rolls[$roll_index + 2] + $this->rolls[$roll_index + 3];
		}
		return false;		
	}
	
	public function extraScoreForStrikeTenthFrame($roll_index) {	
		if ($roll_index == 19 && isset($this->rolls[$roll_index + 1])) {
			return $this->rolls[$roll_index] + $this->rolls[$roll_index + 1];
		}
		else if (isset($this->rolls[$roll_index + 1]) && isset($this->rolls[$roll_index + 2])) {
			return $this->rolls[$roll_index + 1] + $this->rolls[$roll_index + 2];
		}
		return false;		
	}

	public function checkForSpare($roll_index) {
		if (isset($this->rolls[$roll_index]) && isset($this->rolls[$roll_index + 1])) {
			return ($this->rolls[$roll_index] + $this->rolls[$roll_index + 1]) == 10;
		}
		return false;
	}
	
	public function extraScoreForSpare($roll_index) {	
		if (isset($this->rolls[$roll_index + 2])) {
			return $this->rolls[$roll_index + 2];
		}
		return false;
	}
	
	public function extraScoreForSpareTenthFrame($roll_index) {	
		if (isset($this->rolls[20])) {
			return $this->rolls[20];
		}
		return false;
	}
	
	public function standardScore($roll_index) {
		if (isset($this->rolls[$roll_index]) && isset($this->rolls[$roll_index+1])) {
			return $this->rolls[$roll_index] + $this->rolls[$roll_index + 1];
		}
		else if (isset($this->rolls[$roll_index])) {
			return $this->rolls[$roll_index];
		}
		return;
	}

	public function addRoll($rolls, $pins) {
		$rolls[] = $pins;
		return $rolls;
	}
	
	public function addFrameScore($frame_total) {
		$this->frames[] = $frame_total;
	}
	
	public function getCurrentFrame($roll_count) {
		$current_frame = round($roll_count / 2);
		if ($current_frame > 10) {
			$current_frame = 10;
		}
		return $current_frame;
	}

	public function score(Request $request)
	{
		$score = 0;
		//if($request->has('rolls') && $request->has('pins')) {
		if (isset($request->rolls) && isset($request->pins)) {
			$roll_index = 0;
			$this->rolls = $this->addRoll($request->rolls, $request->pins);
			// if bonus roll, frame is 10
			if (count($this->rolls) >= 21) {
				$current_frame = 10;
			}
			else {
				$current_frame = $this->getCurrentFrame(count($this->rolls));
			}		
			for ($frame = 1; $frame <= $current_frame; $frame++) {
				if ($this->checkForStrike($roll_index)) {
					// 10th frame scoring rules$
					if ($frame == 10 && $current_frame == 10) {
						$update_score = $this->extraScoreForStrikeTenthFrame($roll_index);
						if ($update_score != false) {
							$score += 10 + $update_score;
							$this->addFrameScore($score);
							$this->game_over = true;
						}
						$roll_index += 1;
					}
					else {
						$update_score = $this->extraScoreForStrike($roll_index);
						if ($update_score != false) {
							$score += 10 + $update_score;
							$this->addFrameScore($score);
						}
						// if not 10th frame, add 0 to rolls to fill out current frame
						if ($frame == $current_frame) {
							$this->rolls = $this->addRoll($this->rolls, 0);
						}
						$roll_index += 2;
					}					
				}
				else if ($this->checkForSpare($roll_index)) {
					// 10th frame scoring rules
					if ($frame == 10 && $current_frame == 10) {
						$update_score = $this->extraScoreForSpareTenthFrame($roll_index);
						if ($update_score != false) {
							$score += 10 + $update_score;
							$this->addFrameScore($score);
							$this->game_over = true;
						}
						$roll_index += 1;
					}
					else {
						$update_score = $this->extraScoreForSpare($roll_index);
						if ($update_score != false) {
							$score += 10 + $update_score;
							$this->addFrameScore($score);
						}
						$roll_index += 2;
					}
				}
				else {
					$score += $this->standardScore($roll_index);
					$this->addFrameScore($score);
					if ($frame == 10 && $current_frame == 10) {								
						$roll_index += 1;
						if (count($this->rolls) >= 20) {
							$this->game_over = true;
						}
					}
					else {
						$roll_index += 2;
					}
				}
			}
			$this->game_info["score"] = $score;			
			$this->game_info["rolls"] = $this->rolls;
			$this->game_info["frames"] = $this->frames;
			$this->game_info["game_over"] = $this->game_over;
			return json_encode($this->game_info);
		}
		return $score;
	}
}