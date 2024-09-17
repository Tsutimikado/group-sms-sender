import React, {useState} from 'react'
import ArrowSvg from '../img/ArrowSvg';
import HistorySessionBlock from './HistorySessionBlock';

// const text = ` Hello world\nIm going to blow your fucking mind.\nThis is a tty big message to show.\n\nfrom Abdullohonto my dear mutherfucker )) `

const HistoryDayBlock = ({dayData, date}) => {
   const [expanded, setExpanded] = useState(false);

   //-------  Настройка заголовка ----------------
   const today = new Date();
   const yesterday = new Date(today);
   yesterday.setDate(today.getDate() - 1); 
   const dateFormat = (date) => {
      return date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
   }
   const todayString = dateFormat(today);
   const yesterdayString = dateFormat(yesterday);
   let totalMessages = 0;
   Object.entries(dayData).forEach(([id, session]) => {
      totalMessages += session.totalMessages;
   });
   //---------------------------------------------

   return (
      <div className="flex flex-col px-2">
         <div className="flex border-b cursor-pointer day hover:bg-gray-100" title={expanded? 'collapse' : 'expand'} onClick={e=>setExpanded(!expanded)}>
            <h6 className='text-sm text-gray-400 w-full'>{date === todayString ? 'Today' : (date === yesterdayString ? 'Yesterday' : date)} ({totalMessages})</h6>
            <button>
               <ArrowSvg className={'w-6 fill-gray-400 transition-transform ' + (expanded ? 'rotate-[270deg]' : 'rotate-[90deg]')}/>
            </button>
         </div>
         {expanded &&
          <div className='w-full space-y-3 animate-expandY'>
            {Object.keys(dayData).map(sessionId => 
                  <HistorySessionBlock session={dayData[sessionId]} key={sessionId} />
               )}
            
         </div>}
      </div>
   )
}

export default HistoryDayBlock