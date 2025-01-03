import React from "react";
import {Battery} from "../misc/Battery.jsx";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {Hacked} from "../misc/Hacked.jsx";
import {KeySlot} from "../inventory/KeySlot.jsx";
import {InventorySlot} from "../inventory/InventorySlot.jsx";

export const PlayerDetails = ({playerName}) => {

    const battery = useNetrunnersStore(state => state.situation.players[playerName].battery);
    const score = useNetrunnersStore(state => state.situation.players[playerName].score);
    const hasDatabaseKey = useNetrunnersStore(state => state.situation.players[playerName].hasDatabaseKey);
    const hardwareItems = useNetrunnersStore(state => state.situation.players[playerName].hardwareItems);
    const softwareItems = useNetrunnersStore(state => state.situation.players[playerName].softwareItems);
    const itemPickUpType = useNetrunnersStore(state => state.itemPickUpType);

    const classDivCollectedPoints = ' flex items-center justify-center h-[20%] text-[5vh] font-mono uppercase ';
    const classSlotLine = ' flex justify-around items-center py-[4%] sm:py-[2%] px-[2%] ';
    const classSlotElement = ' h-full md:h-[80%] sm:h-[70%] aspect-square border border-solid border-[0.4vh] rounded-lg ';

    const renderHardware = () => Array(2).fill(null).map((_, index) =>
        <InventorySlot key={index} classAdd={classSlotElement} slotKey={index} item={hardwareItems[index]} itemType='Hardware' pickUp={itemPickUpType === 'Hardware'} />
    );

    const renderDatabaseKey = () => Array(1).fill(null).map((_, index) =>
        <KeySlot key={index} classAdd={classSlotElement} hasDatabaseKey={hasDatabaseKey} pickUp={itemPickUpType === 'Key'}/>
    );

    const renderSoftware = () => Array(3).fill(null).map((_, index) =>
        <InventorySlot key={index} classAdd={classSlotElement} slotKey={index} item={softwareItems[index]} itemType='Software' pickUp={itemPickUpType === 'Software'} playerName={playerName}/>
    );

    return (
        <div className='size-full'>

            <div className='flex items-end justify-center w-[100%] h-[80%] rounded-[1vh] '>
                <div className='flex grid grid-rows-3 gap-0 size-full rounded-b-[2vh]'>

                    {/*HARDWARE ITEMS & KEY*/}
                    <div className={classSlotLine}>
                        {renderHardware()}
                        {renderDatabaseKey()}
                    </div>

                    {/*SOFTWARE ITEMS*/}
                    <div className={classSlotLine}>
                        {renderSoftware()}
                    </div>

                    {/*BATTERY AND HACK*/}
                    <div className={classSlotLine}>
                        <div className=' w-[40%] h-[60%] '><Battery points={battery} /></div>
                        <div className=' w-[40%] h-[100%] '><Hacked playerName={playerName} /></div>
                    </div>

                </div>
            </div>

            <div className={classDivCollectedPoints}>
                score: {score}
            </div>

        </div>
    );
}
