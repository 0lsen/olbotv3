<?php

/**
 * @runTestsInSeparateProcesses
 */

class CommandAddJokePositiveTest extends FeatureTestCase
{
    function setup()
    {
        $guzzleMock = Mockery::mock('overload:GuzzleHttp\Client');
        $guzzleMock
            ->shouldReceive('send')
            ->withArgs(function (\GuzzleHttp\Psr7\Request $request){
                return
                    $request->getUri()->getPath() == '/botasd/sendMessage' &&
                    strpos($request->getBody()->getContents(), 'Thank you for your contribution.') !== false;
            })
            ->once()
            ->andReturn(new \GuzzleHttp\Psr7\Response(200));

        $jokeMock = Mockery::mock('alias:OLBot\Model\DB\Joke');
        $jokeMock
            ->shouldReceive('where')
            ->with(['text' => 'foo joke'])
            ->once()
            ->andReturn(new EloquentMock(['count' => 0]));
        $jokeMock
            ->shouldReceive('create')
            ->with(['text' => 'foo joke', 'author' => 789])
            ->once();

        parent::setup();
    }

    function testAddNewJoke()
    {
        $request = [
            'message' => [
                'chat' => [
                    'id' => 789
                ],
                'from' => [
                    'id' => 789
                ],
                'text' => '/addJoke foo joke'
            ]
        ];

        $this->client->post('/incoming', $request);
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }
}