<?php
declare(strict_types=1);

namespace Gaming\ConnectFour\Port\Adapter\Persistence\Mapping;

use Gaming\Common\ObjectMapper\DiscriminatorMapper;
use Gaming\Common\ObjectMapper\Exception\MapperException;
use Gaming\Common\ObjectMapper\Mapper;
use Gaming\Common\ObjectMapper\ObjectMapper;
use Gaming\Common\ObjectMapper\Scalar\IntMapper;
use Gaming\ConnectFour\Domain\Game\State\Aborted;
use Gaming\ConnectFour\Domain\Game\State\Drawn;
use Gaming\ConnectFour\Domain\Game\State\Open;
use Gaming\ConnectFour\Domain\Game\State\Resigned;
use Gaming\ConnectFour\Domain\Game\State\Running;
use Gaming\ConnectFour\Domain\Game\State\Won;

final class StateMapper implements Mapper
{
    /**
     * @var DiscriminatorMapper $discriminatorMapper
     */
    private DiscriminatorMapper $discriminatorMapper;

    /**
     * StateMapper constructor.
     *
     * @param WinningRuleMapper   $winningRuleMapper
     * @param BoardMapper         $boardMapper
     * @param PlayerMapper        $playerMapper
     * @param PlayersMapper       $playersMapper
     * @param ConfigurationMapper $configurationMapper
     *
     * @throws MapperException
     */
    public function __construct(
        WinningRuleMapper $winningRuleMapper,
        BoardMapper $boardMapper,
        PlayerMapper $playerMapper,
        PlayersMapper $playersMapper,
        ConfigurationMapper $configurationMapper
    ) {
        $runningMapper = new ObjectMapper(Running::class);
        $runningMapper->addProperty('winningRule', $winningRuleMapper);
        $runningMapper->addProperty('numberOfMovesUntilDraw', new IntMapper());
        $runningMapper->addProperty('board', $boardMapper);
        $runningMapper->addProperty('players', $playersMapper);

        $openMapper = new ObjectMapper(Open::class);
        $openMapper->addProperty('player', $playerMapper);
        $openMapper->addProperty('configuration', $configurationMapper);

        $abortedMapper = new ObjectMapper(Aborted::class);

        $resignedMapper = new ObjectMapper(Resigned::class);

        $drawnMapper = new ObjectMapper(Drawn::class);

        $wonMapper = new ObjectMapper(Won::class);

        $stateDiscriminatorMapper = new DiscriminatorMapper('type');
        $stateDiscriminatorMapper->addDiscriminator(
            Running::class,
            $runningMapper,
            'running'
        );
        $stateDiscriminatorMapper->addDiscriminator(
            Open::class,
            $openMapper,
            'open'
        );
        $stateDiscriminatorMapper->addDiscriminator(
            Aborted::class,
            $abortedMapper,
            'aborted'
        );
        $stateDiscriminatorMapper->addDiscriminator(
            Resigned::class,
            $resignedMapper,
            'resigned'
        );
        $stateDiscriminatorMapper->addDiscriminator(
            Drawn::class,
            $drawnMapper,
            'drawn'
        );
        $stateDiscriminatorMapper->addDiscriminator(
            Won::class,
            $wonMapper,
            'won'
        );

        $this->discriminatorMapper = $stateDiscriminatorMapper;
    }

    /**
     * @inheritdoc
     */
    public function serialize($value)
    {
        return $this->discriminatorMapper->serialize($value);
    }

    /**
     * @inheritdoc
     */
    public function deserialize($value)
    {
        return $this->discriminatorMapper->deserialize($value);
    }
}
