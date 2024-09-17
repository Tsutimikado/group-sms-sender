import React from 'react'

const Confirmation = ({setConfirmation, confirm}) => {
  return (
     <div className='absolute w-full h-full top-0 left-0 bg-gray-100/[90%] z-10 border-4 rounded-xl border-gray-200 flex flex-col justify-center items-center text-gray-500 animate-appear' >
        <p className='font-semibold mb-4 bg-gradient-to-t from-gray-100/[90%] to-gray-100'>Сохранить изменения?</p>
        <div className='space-y-1'>
           <div className='flex justify-center space-x-3'>
              <button className='btn-confirm_colors btn-confirm_style animate-appearDropRight' onClick={e=> confirm(true)}>Сохранить</button>
              <button className='btn-confirm_colors btn-confirm_style animate-appearDropLeft' onClick={e => confirm(false)}>Отменить</button>
           </div>
           <button className='btn-confirm_colors btn-confirm_style animate-appearDropUp' onClick={e=>setConfirmation(false)}>Вернуться к редактированию</button>
        </div>
     </div>
  )
}

export default Confirmation