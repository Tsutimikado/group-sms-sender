import { useState } from 'react';
import React from 'react'
import copy from 'copy-to-clipboard';

const HistorySessionBlock = ({ session }) => {
   const [expanded, setExpanded] = useState(false);
   
   const formatNumber = (number) => { //переводит формат номера из 000000000 в +992 000-00-00-00;
      return number.replace(/^(\d{3})(\d{2})(\d{2})(\d{2})$/, '+992 $1-$2-$3-$4');
   }
   return (
      <div className='w-full flex flex-col '>
         <div className="flex items-center hover:bg-gray-100 mb-1" title={expanded ? 'collapse' : 'expand'} onClick={e => setExpanded(!expanded)}>
            <div className={'h-[2px] rounded-full flex-grow mx-2 ' + (expanded ? 'bg-gray-400' : 'bg-gray-300')}></div>
            <h6 className='text-gray-600'>[ {session.sessionStart} ] <span className='text-gray-400'>From: {session.from}</span></h6>
            <div className={'h-[2px] rounded-full flex-grow mx-2 ' + (expanded ? 'bg-gray-400' : 'bg-gray-300')}></div>
         </div>
         {expanded &&
         <div className=' border-l-2 pl-2 border-gray-400 flex '>
            <div className='w-3/5 rounded-r-xl bg-gray-100/75'>
               <h6 className='text-gray-500'>SMS Text: </h6>
               <p className='w-full whitespace-pre-line h-fit border-l-2 active:outline-none
                                    focus:outline-none pl-3 my-2 border-blue-400 text-gray-700 break-words '>
                  {session.content}
               </p>
            </div>
            <div className='w-2/5 border-l-4 border-white pl-3 rounded-l-xl bg-gray-100/75'>
               <h6 className='text-gray-500'>Numbers: </h6>
               <div className="columns-[150px_auto] border-l-2 border-pink-300 pl-2 my-2 space-y-[1px]">
                  { session.numbers.map(n=>
                     <p className='hover:scale-110 hover:bg-gray-200 rounded-md transition-transform cursor-pointer' 
                     title='copy'
                     onClick={e => copy(formatNumber(n))}
                     key={n}
                     >{formatNumber(n)}</p>
                  )}
               </div>
            </div>
         </div>}
      </div>
   )
}

export default HistorySessionBlock