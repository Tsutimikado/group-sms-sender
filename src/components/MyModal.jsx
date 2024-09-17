import React from 'react'
// import cl from './MyModal.module.css';

const MyModal = ({children, visible, setVisible}) => {

   const visClasses = 'modal-classes flex justify-center items-center z-10  '
   const invisClasses = 'modal-classes hidden'

   return (
      <div className = {visible ?visClasses :invisClasses} onClick = {() => setVisible(false)}>
         <div className='p-5 bg-[#fbfbfb] rounded-lg space-y-2 ' onClick = {(e)=>e.stopPropagation()}>
            {children}
         </div>
         
      </div>
   )
}

export default MyModal