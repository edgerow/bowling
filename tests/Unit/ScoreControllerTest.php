<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\Api\ScoreController;

class ScoreControllerTest extends TestCase
{		
    public function testAddRoll()
    {
		$scoreController = new ScoreController;
		$scoreController->rolls = array();
		$pins = 1;
		$scoreController->rolls = $scoreController->addRoll($scoreController->rolls, $pins);
		$expectedArray = [1];
		$this->assertCount(1, $scoreController->rolls);
		$this->assertEquals($scoreController->rolls, $expectedArray );
		$pins = 10;
		$scoreController->rolls = $scoreController->addRoll($scoreController->rolls, $pins);
		$expectedArray = [1, 10];
		$this->assertCount(2, $scoreController->rolls);
		$this->assertEquals($scoreController->rolls, $expectedArray);
    }
	public function testGetCurrentFrame() {		
		$scoreController = new ScoreController;
		$scoreController->rolls = array();
		$scoreController->rolls = $scoreController->addRoll($scoreController->rolls, 1);
		$frameCountFromFunction = $scoreController->getCurrentFrame(count($scoreController->rolls));
		$this->assertEquals($frameCountFromFunction, 1);
		$scoreController->rolls = $scoreController->addRoll($scoreController->rolls, 2);
		$frameCountFromFunction = $scoreController->getCurrentFrame(count($scoreController->rolls));
		$this->assertEquals($frameCountFromFunction, 1);
		$scoreController->rolls = $scoreController->addRoll($scoreController->rolls, 3);
		$frameCountFromFunction = $scoreController->getCurrentFrame(count($scoreController->rolls));
		$this->assertEquals($frameCountFromFunction, 2);
		$scoreController->rolls = $scoreController->addRoll($scoreController->rolls, 4);
		$frameCountFromFunction = $scoreController->getCurrentFrame(count($scoreController->rolls));
		$this->assertEquals($frameCountFromFunction, 2);
		$scoreController->rolls = $scoreController->addRoll($scoreController->rolls, 10);
		$frameCountFromFunction = $scoreController->getCurrentFrame(count($scoreController->rolls));
		$this->assertEquals($frameCountFromFunction, 3);
		$scoreController->rolls = $scoreController->addRoll($scoreController->rolls, 10);
		$frameCountFromFunction = $scoreController->getCurrentFrame(count($scoreController->rolls));
		$this->assertEquals($frameCountFromFunction, 3);
	}
	
	public function testCheckForStrike()
	{
		$scoreController = new ScoreController;
		$scoreController->rolls = array();
		$scoreController->rolls = $scoreController->addRoll($scoreController->rolls, 10);
		$expectedArray = [10]; 
		$this->assertEquals($scoreController->rolls, $expectedArray);
		$roll_index = 0;
		$isStrike = $scoreController->checkForStrike($roll_index);
		$this->assertTrue($isStrike);
		$scoreController->rolls = $scoreController->addRoll($scoreController->rolls, 7);
		$roll_index = 1;
		$isStrike = $scoreController->checkForStrike($roll_index);
		$this->assertFalse($isStrike);
	}
	public function testAddFrameScore()
    {
		$scoreController = new ScoreController;
		$scoreController->rolls = array();
		$scoreController->frames = [];
		$frame_score = 9;
		$scoreController->addFrameScore($frame_score);
		$expectedArray = [9];
		$this->assertCount(1, $scoreController->frames);
		$this->assertEquals($scoreController->frames, $expectedArray );
		$pins = 8;
		$scoreController->addFrameScore($pins);
		$expectedArray = [9, 8];
		$this->assertCount(2, $scoreController->frames);
		$this->assertEquals($scoreController->frames, $expectedArray);
    }
	public function testGettingScoreForOpenFrameFollowedBySpareFollowedByOpenFrame()
	{
		$scoreController = new ScoreController;
		$scoreController->rolls = array();
		$request = $this->getMockBuilder('Illuminate\Http\Request')
        ->disableOriginalConstructor()
        ->getMock();
		$request->rolls = [5,3,5,5,5];		
		$request->pins = 3;
		$game_info = $scoreController->score($request);
		$expected_score = 31;
		$expected_rolls_array = '[5,3,5,5,5,3]';
		$expected_frames_array = '[8,23,31]';
		$expected_array = '{"score":' . strval($expected_score) . ',"rolls":' . $expected_rolls_array . ',"frames":' . $expected_frames_array . ',"game_over":false}';	
		$this->assertEquals($game_info, $expected_array);
	}
	public function testForTwoStrikesNotToScoreFirstFrame()
	{
		$scoreController = new ScoreController;
		$scoreController->rolls = array();
		$request = $this->getMockBuilder('Illuminate\Http\Request')
        ->disableOriginalConstructor()
        ->getMock();
		$request->rolls = [10,0];		
		$request->pins = 10;
		$game_info = $scoreController->score($request);	
		$expected_score = 0;
		$expected_frames_array = '[]';
		$expected_rolls_array = '[10,0,10,0]';
		$expected_array = '{"score":' . strval($expected_score) . ',"rolls":' . $expected_rolls_array . ',"frames":' . $expected_frames_array . ',"game_over":false}';	
		$this->assertEquals($game_info, $expected_array);
	}
	public function testForThreeStrikesInARowToScore30inFirstFrame()
	{
		$scoreController = new ScoreController;
		$scoreController->rolls = array();
		$request = $this->getMockBuilder('Illuminate\Http\Request')
        ->disableOriginalConstructor()
        ->getMock();
		$request->rolls = [10,0,10,0];		
		$request->pins = 10;
		$game_info = $scoreController->score($request);	
		$expected_score = 30;
		$expected_frames_array = '[30]';
		$expected_rolls_array = '[10,0,10,0,10,0]';
		$expected_array = '{"score":' . strval($expected_score) . ',"rolls":' . $expected_rolls_array . ',"frames":' . $expected_frames_array . ',"game_over":false}';	
		$this->assertEquals($game_info, $expected_array);
	}
	public function testForFourStrikesInARowToScore60inSecondFrame()
	{
		$scoreController = new ScoreController;
		$scoreController->rolls = array();
		$request = $this->getMockBuilder('Illuminate\Http\Request')
        ->disableOriginalConstructor()
        ->getMock();
		$request->rolls = [10,0,10,0,10,0];		
		$request->pins = 10;
		$game_info = $scoreController->score($request);	
		$expected_score = 60;
		$expected_frames_array = '[30,60]';
		$expected_rolls_array = '[10,0,10,0,10,0,10,0]';
		$expected_array = '{"score":' . strval($expected_score) . ',"rolls":' . $expected_rolls_array . ',"frames":' . $expected_frames_array . ',"game_over":false}';	
		$this->assertEquals($game_info, $expected_array);
	}
	public function testForScoringThreeStrikesInTenthFrame()
	{
		$scoreController = new ScoreController;
		$scoreController->rolls = array();		
		$this->assertFalse($scoreController->game_over);
		$request = $this->getMockBuilder('Illuminate\Http\Request')
        ->disableOriginalConstructor()
        ->getMock();
		$request->rolls = [10,0,10,0,10,0,10,0,10,0,10,0,10,0,10,0,10,0,10,10];		
		$request->pins = 10;
		$game_info = $scoreController->score($request);
		$expected_score = 300;
		$expected_rolls_array = '[10,0,10,0,10,0,10,0,10,0,10,0,10,0,10,0,10,0,10,10,10]';
		$expected_frames_array = '[30,60,90,120,150,180,210,240,270,300]';
		$expected_array = '{"score":' . strval($expected_score) . ',"rolls":' . $expected_rolls_array . ',"frames":' . $expected_frames_array . ',"game_over":true}';	
		$this->assertEquals($game_info, $expected_array);
	}
	public function testForScoringSpareInTenthFrame()
	{
		$scoreController = new ScoreController;
		$scoreController->rolls = array();		
		$this->assertFalse($scoreController->game_over);
		$request = $this->getMockBuilder('Illuminate\Http\Request')
        ->disableOriginalConstructor()
        ->getMock();
		$request->rolls = [10,0,10,0,10,0,10,0,10,0,10,0,10,0,10,0,10,0,5,5];		
		$request->pins = 5;
		$game_info = $scoreController->score($request);
		$expected_score = 270;
		$expected_rolls_array = '[10,0,10,0,10,0,10,0,10,0,10,0,10,0,10,0,10,0,5,5,5]';
		$expected_frames_array = '[30,60,90,120,150,180,210,235,255,270]';
		$expected_array = '{"score":' . strval($expected_score) . ',"rolls":' . $expected_rolls_array . ',"frames":' . $expected_frames_array . ',"game_over":true}';	
		$this->assertEquals($game_info, $expected_array);
	}	
	public function testGettingScoreForGutterGame()
	{
		$scoreController = new ScoreController;
		$scoreController->rolls = array();
		$request = $this->getMockBuilder('Illuminate\Http\Request')
        ->disableOriginalConstructor()
        ->getMock();
		$request->rolls = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];		
		$request->pins = 0;
		$game_info = $scoreController->score($request);
		$this->assertCount(20, $scoreController->rolls);		
		$frameCountFromFunction = $scoreController->getCurrentFrame(count($scoreController->rolls));
		$this->assertEquals($frameCountFromFunction, 10);
		$expected_score = 0;
		$expected_rolls_array = '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]';
		$expected_frames_array = '[0,0,0,0,0,0,0,0,0,0]';
		$expected_array = '{"score":' . strval($expected_score) . ',"rolls":' . $expected_rolls_array . ',"frames":' . $expected_frames_array . ',"game_over":true}';		
		$this->assertEquals($game_info, $expected_array);
	}	
	public function testGettingScoreForGameWithRollsOfOne()
	{
		$scoreController = new ScoreController;
		$scoreController->rolls = array();
		$request = $this->getMockBuilder('Illuminate\Http\Request')
        ->disableOriginalConstructor()
        ->getMock();
		$request->rolls = [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];		
		$request->pins = 1;
		$game_info = $scoreController->score($request);
		$this->assertCount(20, $scoreController->rolls);		
		$frameCountFromFunction = $scoreController->getCurrentFrame(count($scoreController->rolls));
		$this->assertEquals($frameCountFromFunction, 10);
		$expected_score = 20;
		$expected_rolls_array = '[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1]';
		$expected_frames_array = '[2,4,6,8,10,12,14,16,18,20]';
		$expected_array = '{"score":' . strval($expected_score) . ',"rolls":' . $expected_rolls_array . ',"frames":' . $expected_frames_array . ',"game_over":true}';		
		$this->assertEquals($game_info, $expected_array);
	}	
	public function testGettingScoreForStrikeThenSpareThen4()
	{
		$scoreController = new ScoreController;
		$scoreController->rolls = array();		
		$this->assertFalse($scoreController->game_over);
		$request = $this->getMockBuilder('Illuminate\Http\Request')
        ->disableOriginalConstructor()
        ->getMock();
		$request->rolls = [10,0,5,5,4];		
		$request->pins = 0;
		$game_info = $scoreController->score($request);
		$this->assertCount(6, $scoreController->rolls);		
		$frameCountFromFunction = $scoreController->getCurrentFrame(count($scoreController->rolls));
		$this->assertEquals($frameCountFromFunction, 3);
		$expected_score = 38;
		$expected_rolls_array = '[10,0,5,5,4,0]';
		$expected_frames_array = '[20,34,38]';
		$expected_array = '{"score":' . strval($expected_score) . ',"rolls":' . $expected_rolls_array . ',"frames":' . $expected_frames_array . ',"game_over":false}';	
		$this->assertEquals($game_info, $expected_array);
	}
	public function testGettingScoreForGameOfSpares()
	{
		$scoreController = new ScoreController;
		$scoreController->rolls = array();		
		$this->assertFalse($scoreController->game_over);
		$request = $this->getMockBuilder('Illuminate\Http\Request')
        ->disableOriginalConstructor()
        ->getMock();
		$request->rolls = [5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5];		
		$request->pins = 5;
		$game_info = $scoreController->score($request);
		$this->assertCount(21, $scoreController->rolls);		
		$frameCountFromFunction = $scoreController->getCurrentFrame(count($scoreController->rolls));
		$this->assertEquals($frameCountFromFunction, 10);
		$expected_score = 150;
		$expected_rolls_array = '[5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5]';
		$expected_frames_array = '[15,30,45,60,75,90,105,120,135,150]';
		$expected_array = '{"score":' . strval($expected_score) . ',"rolls":' . $expected_rolls_array . ',"frames":' . $expected_frames_array . ',"game_over":true}';	
		$this->assertEquals($game_info, $expected_array);
	}	
	public function testGettingScoreForPerfectGame()
	{
		$scoreController = new ScoreController;
		$scoreController->rolls = array();		
		$this->assertFalse($scoreController->game_over);
		$request = $this->getMockBuilder('Illuminate\Http\Request')
        ->disableOriginalConstructor()
        ->getMock();
		$request->rolls = [10,0,10,0,10,0,10,0,10,0,10,0,10,0,10,0,10,0,10,10];		
		$request->pins = 10;
		$game_info = $scoreController->score($request);
		$this->assertCount(21, $scoreController->rolls);		
		$frameCountFromFunction = $scoreController->getCurrentFrame(count($scoreController->rolls));
		$this->assertEquals($frameCountFromFunction, 10);
		$expected_score = 300;
		$expected_rolls_array = '[10,0,10,0,10,0,10,0,10,0,10,0,10,0,10,0,10,0,10,10,10]';
		$expected_frames_array = '[30,60,90,120,150,180,210,240,270,300]';
		$expected_array = '{"score":' . strval($expected_score) . ',"rolls":' . $expected_rolls_array . ',"frames":' . $expected_frames_array . ',"game_over":true}';	
		$this->assertEquals($game_info, $expected_array);
	}
}
