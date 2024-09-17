import React, { useEffect, useState } from 'react'
import HistoryDayBlock from './HistoryDayBlock';
import axios from 'axios';
// const seansText = 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Non nostrum dolores beatae placeat corporis vel omnis animi, sequi, qui fugit aut possimus porro facilis officia provident et temporibus vero tempore.';




const HistoryBar = () => {
   const [showHistory, setShowHistory] = useState(false);
   const [history, setHistory] = useState({});
   // useEffect
   const getHistory = async () => {
      try {
         // const response = await axios.get('http://localhost/get_history.php');
         const response = await axios.get('./get_history.php');
         // const response = await axios.get('http://cm75198.tw1.ru/get_history.php');
         setHistory(JSON.parse(response.data))
         // console.log(JSON.parse(response.data));
      }
      catch (e) {
         console.log(e);
      }

   };

   useEffect(() => {
      getHistory();
   },
      [showHistory]);

   return (
      <div className='absolute top-0 left-0 bg-black/50 z-10 w-full'>
         <div className='relative w-fit shadow-black'>
            <button className=' expand-history-btn' onClick={e => setShowHistory(!showHistory)}>
               <span>H</span><div className='w-0 overflow-clip istory-span transition-[width] '>{showHistory ? 'ide' : 'istory'}</div>
            </button>
            {showHistory && <aside className={'border-r-2 h-screen shadow-2xl shadow-black bg-white w-fit animate-appear overflow-auto'}>
               <h3 className='text-lg text-center py-2 border-b bg-gray-700 text-white'>History</h3>
               <div className='w-[900px] mt-2 space-y-2 px-1' >
                  {Object.keys(history).map(key =>
                     <HistoryDayBlock dayData={history[key]} date={key} key={key}/>
                  )}
               </div>
            </aside>}
         </div>
      </div>
 
   )
}

export default HistoryBar