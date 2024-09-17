import React from 'react'

const Loader = () => {
   return (
      <div className='flex flex-col items-center cursor-wait'>
         <div className='mb-1 text-xl'>Загрузка...</div>
         <div 
            className=' w-9 h-9 rounded-full
            border-2 border-gray-700
            flex justify-center
            animate-[spin_1.5s_linear_infinite]'
         > 
            <div
               className='bg-gray-700 h-1/2 w-[2px] rounded-full'
            ></div>
         </div>
      </div>
   )
}
// const Loader = () => {
//    const anima = ' '
//    return (
//       <div className='flex items-center'>
//          <div className='mb-1 text-xl'>Загрузка</div>
//          <div className={'dot-drift '+anima}>.</div>
//          <div className={'animate-[drift_3s_ease-in-out_infinite] delay-200'+anima}>.</div>
//          <div className={'animate-[drift_3s_ease-in-out_infinite]'+anima}>.</div>
//       </div>
//    )
// }

export default Loader