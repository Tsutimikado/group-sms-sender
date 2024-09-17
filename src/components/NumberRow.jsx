import React, { useState, useEffect } from 'react'
import copy from 'copy-to-clipboard'
import InputMask from 'react-input-mask'

const NumberRow = ({ number, classes, deleteFromGroup, isEditMode, changeNumber, validate }) => {
   const [inputValue, setInputValue] = useState(number);
   const [statusClasses, setStatusClasses] = useState('');

   useEffect(() => {
      if (validate(inputValue)) {
         setStatusClasses('');
         changeNumber(inputValue);
      }
      else {
         setStatusClasses('bg-red-100')
      }
   }, [inputValue])
   
   const handleInputValChange = (val) => {
      if (validate(val)) {
         setStatusClasses('');
         changeNumber(val);
      }
      else {
         setStatusClasses('bg-red-100')
      }
      setInputValue(val);
      // changeNumber(val);
   }

   const saveStateOnBlur = (e) => {
      if(validate(inputValue)){
         changeNumber(inputValue);
      }
      else {
         setInputValue(number);
      }

   }

   const saveStateOnKey = (e) => {
      if(e.key === 'Enter') {
         if (validate(inputValue)) {
            changeNumber(inputValue);
         }
      }
      if(e.key === 'Escape') {
         setInputValue(number);
      }
   }

   return (
      <div className={"py-2 px-5 border-b flex items-center transition-colors duration-[500ms] " + classes + statusClasses}
         onClick={!isEditMode ? (e => copy(number)) : null}
         title={isEditMode ? 'edit' : 'copy'}
      >
         {isEditMode
            ? <InputMask
               // id={'new-num-input-' + group.groupId}
               mask="+\9\92 999-99-99-99"
               maskChar="_"
               onChange={e => setInputValue(e.target.value)}
               className=' w-full focus:outline-none bg-inherit '
               value={inputValue}
               onKeyDown={saveStateOnKey}
               onBlur={saveStateOnBlur}
            // autoFocus={true}
            />
            : <span className='w-full'>{number}</span>}
         <div
            onClick={e => deleteFromGroup(number)}
            className={"exit__custom relative aspect-square w-5 h-5 mt-1 cursor-pointer hover:scale-[1.17] active:scale-90 " + (isEditMode ? '' : 'hidden')}>
            <div className="rounded-full w-full h-[3px] bg-gray-800 absolute top-1/2 -translate-y-1/2 rotate-45"></div>
            <div className="rounded-full h-full w-[3px] bg-gray-800 absolute left-1/2 -translate-x-1/2 rotate-45"></div>
         </div>
      </div>
   )
}

export default NumberRow