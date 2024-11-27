import React from "react";
import {configNetrunners} from "../../configNetrunners.jsx";
import {Attribute} from "../misc/Attribute.jsx";

export const EncounterDetails = ({classEncounter, classDetails, itemKey, name, power, itemType = null, description = null, action = undefined}) => {

    console.log('EncounterDetails', itemKey, name, itemType, description !== null);

    const classDivDetails = classDetails
        + ' relative items-end justify-center bg-top bg-no-repeat bg-cover '
        + ' w-[100%] h-[80%] rounded-[1vh] border-solid border-[2px] '
        + (action !== undefined
            ? ' border-orange-500 cursor-pointer shadow-actionSm hover:shadow-actionLg '
            : ' border-cyan-500 shadow-actionSmOp ');

    const style = {
        backgroundImage: configNetrunners.encounters[itemKey].imageM,
    };

    const classDivName = ' flex items-center justify-center h-[20%] text-[5vh] font-mono lowercase '
        + (!!itemType ? configNetrunners.encounters.classItemText : configNetrunners.encounters.classEnemyText);

    return (
        <div className={classEncounter}>

            <div className={classDivDetails} style={style} onClick={action}>

                <div className=' absolute bottom-0 w-[98%] px-[1%] sm:w-[96%] sm:px-[2%] leading-normal sm:leading-normal bg-neutral-900/90 rounded-b-[0.8vh] text-lime-500 text-[2.5vh] sm:text-sm font-mono'>
                    {description}
                </div>

                {power > 0 &&
                    <Attribute className=' absolute top-[1vh] right-[1vh] border-pink-600 text-pink-600 '
                               sizeVh={8}
                               sizeVhSm={6}>
                        {power}
                    </Attribute>
                }

            </div>

            <div className={classDivName}>
                {name}
            </div>

        </div>
    );
}
