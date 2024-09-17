import React, { useState, useEffect, useRef } from 'react'
import { v4 as uuidv4 } from 'uuid';
import InputMask from 'react-input-mask';
import NumberRow from './NumberRow';
import EditConfirmation from './EditConfirmation';
import DeleteConfirmation from './DeleteConfirmation'
import TrashSvg from '../img/TrashSvg';


const GroupBox = ({ group, setGroup, validateGroupName, className, deleteGroup }) => {
   const [width, setWidth] = useState(0);
   const span = useRef();
   const [numbers, setNumbers] = useState(group.numbers);
   const [groupName, setGroupName] = useState(group.groupId);
   // const [cacheNumbers, setCacheNumbers] = useState(group.numbers);
   const [isEditMode, setIsEditMode] = useState(false);
   const [newNum, setNewNum] = useState('');
   const [showNewNumInput, setShowNewNumInput] = useState(false);
   const [isErrorMode, setIsErrorMode] = useState(false);
   const [inputStatusClasses, setInputStatusClasses] = useState('');
   const [confirmation, setConfirmation] = useState(false);
   const [wasEdited, setWasEdited] = useState(false);

   useEffect(() => {
      if (!isEditMode) {
         setGroup({ ...group, numbers: numbers });
      }
      else setWasEdited(true);
      // setCacheNumbers(numbers);
   },
      [numbers, groupName])

   useEffect(() => {
      if (showNewNumInput) {
         // Фокусируем поле ввода, когда showNewNumInput установлено в true
         document.getElementById('new-num-input-' + group.groupId)?.focus();
      }
   }, [showNewNumInput]);

   useEffect(() => {
      const SpanOffset = span.current.offsetWidth;
      if (SpanOffset !== 0 && SpanOffset < 145)
         setWidth(SpanOffset);
      else if (SpanOffset > 144) setWidth('145px');
      else setWidth('5px');
   }, [groupName]);
   // const calculatePosition = () => {
   //    const buttonRect = addGroupButton.current.getBoundingClientRect();
   //    const selectorHeight = groupSelectorRef.current.clientHeight;
   //    const top = buttonRect.bottom + window.scrollY;
   //    const left = buttonRect.right + window.scrollX;
   //    if (buttonRect.bottom < selectorHeight) {
   //       return { top: window.scrollY, left };
   //    }
   //    return { top: top - selectorHeight, left };
   // };

   // const setPosition = () => {
   //    const { top, left } = calculatePosition();
   //    const groupSelector = groupSelectorRef.current;

   //    if (groupSelector) {
   //       groupSelector.style.position = 'absolute';
   //       groupSelector.style.top = `${top}px`;
   //       groupSelector.style.left = `${left}px`;
   //    }
   // };

   const handleNewNumInputChange = (e) => {
      const val = e.target.value;
      if (numbers.includes(val)) {
         setIsErrorMode(true);
         // setNumbers(numbers.map((numObject) =>{
         //    console.log(val, numObject.number === val);
         //    return numObject.number === val ? { ...numObject, className: 'bg-red-100' } : numObject;
         // }));
         setInputStatusClasses('bg-red-100')
      }
      else { setIsErrorMode(false) };
      setNewNum(val);
   }

   const validateNum = (number) => {
      // const checklist = cacheNumbers.filter(n => n === number);
      const checklist = numbers.filter(n => n === number);
      const pattern = /^\+992 \d{3}-\d{2}-\d{2}-\d{2}$/;
      return (checklist.length <= 1 && pattern.test(number))
   }

   const checkNewNumAndSave = (inputValue) => {
      inputValue = inputValue.replaceAll('-_', '');
      inputValue = inputValue.replaceAll('_', '');
      const pattern = /^\+992 \d{3}-\d{2}-\d{2}-\d{2}$/;
      if (pattern.test(inputValue) && inputValue !== '' && !numbers.includes(inputValue)) {
         setNumbers([
            ...numbers,
            inputValue,
         ])
      }
      setNewNum('');
   }
   const saveNewNumOnEnter = (e) => {
      let inputValue = e.target.value;
      if (e.key === 'Enter') {
         if (isErrorMode || inputValue.includes('_')) {
            setIsErrorMode(true);
            const errorClasses = 'ring-2 ring-red-400 animate-wiggle bg-red-100';
            setInputStatusClasses(errorClasses);
            setTimeout(() => {
               setInputStatusClasses('transition-all bg-red-100');
            }, 700);
         }
         else if (!isErrorMode) {
            checkNewNumAndSave(inputValue);
            setShowNewNumInput(false);
         }
      }
      if (e.key === 'Escape') {
         clearInputAndClose();
      }

   }

   const saveNewNumOnBlur = (e) => {
      let inputValue = e.target.value;
      checkNewNumAndSave(inputValue);
      setShowNewNumInput(false);
   }

   const clearInputAndClose = (e) => {
      setShowNewNumInput(false);
      setNewNum('');
   }

   const deleteFromGroup = (number) => {
      setNumbers(numbers.filter(n => n !== number));
   }

   const handleSaveEditBtnClick = (e) => {
      if (isEditMode) {
         const groupNameValidationResult = validateGroupName(group.groupId, groupName);
         if (wasEdited) {
            if (groupNameValidationResult) setConfirmation('edit');
         }
         else setIsEditMode(false);
      }
      else setIsEditMode(true);
   }

   const handleKeyDownOnGroupNameInput = (e) => {
      if (e.key === 'Enter') {
         const groupNameValidationResult = validateGroupName(group.groupId, groupName);
         if (wasEdited) {
            if (groupNameValidationResult) setConfirmation('edit');
         }
         else setIsEditMode(false);
      }
      if (e.key === 'Escape') {
         const groupNameValidationResult = validateGroupName(group.groupId, groupName);
         if (wasEdited) {
            if (groupNameValidationResult) setConfirmation('edit');
         }
         else setIsEditMode(false);
      }
   }

   const handleDeleteBtnClick = (e) => {
      setConfirmation('delete');
   }

   const confirmEdit = (bool) => {
      if (wasEdited) {
         // const groupNameValidationResult = validateGroupName(group.groupId, groupName);
         if (bool) setGroup({
            groupId: groupName,
            numbers: numbers
         })
         // else if (!groupNameValidationResult) {

         // }
         else {
            setNumbers(group.numbers);
            setGroupName(group.groupId);
         }
         setIsEditMode(false);
      }
      setConfirmation(false);
      setWasEdited(false);
   }

   const confirmDelete = (bool) => {
      if (bool) deleteGroup();
      setConfirmation(false);
   }

   const createNumberChanger = (index) => {
      return (newNumber) => {
         setNumbers([...numbers.slice(0, index), newNumber, ...numbers.slice(index + 1)]);
      }
   }

   return (
      <div
         className={"rounded-xl shadow-lg w-[240px] border-t border-x overflow-hidden h-fit relative" + ' ' + className}
         key={group.groupId}>
         <span id="hide" ref={span} className='text-lg font-semibold absolute z-0 -translate-x-[100%] opacity-0'>{groupName}</span>
         {confirmation === 'edit' && <EditConfirmation setConfirmation={setConfirmation} confirm={confirmEdit} />}
         {confirmation === 'delete' && <DeleteConfirmation setConfirmation={setConfirmation} confirm={confirmDelete} />}
         <div className="flex px-2 border-b border-b-gray-300 bg-gray-200 pt-3 transition-colors justify-between">
            <div className='flex pl-4'>
               {isEditMode
                  ? <input
                     type="text"
                     value={groupName}
                     onChange={e => setGroupName(e.target.value)}
                     onKeyDown={handleKeyDownOnGroupNameInput}
                     className={" text-lg font-semibold text-gray-700 bg-inherit focus:outline-none"}
                     style={{ width }} />
                  : <h5
                     className=" text-lg font-semibold text-gray-700 break-all">
                     {group.groupId}
                  </h5>}
               <button
                  className='text-sm rounded-md text-gray-500 transition-all mx-1 px-1 active:bg-slate-100'
                  onClick={handleSaveEditBtnClick}
               >{isEditMode ? 'save' : 'edit'}
               </button>
            </div>
            <button onClick={handleDeleteBtnClick}>
               <TrashSvg className='fill-gray-400 w-5 ' />
            </button>
         </div>
         <div className=" text-gray-800 text-lg max-h-[271px] overflow-auto">
            {isEditMode ?
               numbers.map((number, index) => (
                  <NumberRow
                     number={number}
                     classes={number === newNum ? 'bg-red-100' : ''}
                     key={group.groupId + number + index}
                     deleteFromGroup={deleteFromGroup}
                     isEditMode={isEditMode}
                     changeNumber={createNumberChanger(index)}
                     validate={validateNum} />
               ))
               : group.numbers.map((number, index) => (
                  <NumberRow
                     number={number}
                     classes={number === newNum ? 'bg-red-100' : ''}
                     key={group.groupId + number + index}
                     deleteFromGroup={deleteFromGroup}
                     isEditMode={isEditMode}
                     changeNumber={createNumberChanger(index)}
                     validate={validateNum} />
               ))
            }
            <div
               id={'add-number-to-group-' + group.groupId}
               className={'py-2 px-5 border-b bg-gray-50/50 flex items-center transition-colors duration-[500ms] '
                  + (showNewNumInput ? ' ' : 'hidden ')
                  + (isErrorMode ? inputStatusClasses : '')} >
               <InputMask
                  id={'new-num-input-' + group.groupId}
                  mask="+\9\92 999-99-99-99"
                  maskChar="_"
                  onChange={handleNewNumInputChange}
                  onBlur={saveNewNumOnBlur}
                  onKeyDown={saveNewNumOnEnter}
                  className='text-center w-full focus:outline-none bg-inherit'
                  value={newNum}
               />
               <div
                  className="exit__custom relative aspect-square w-5 h-5 mt-1 cursor-pointer hover:scale-110 active:scale-90"
                  onMouseDown={(e) => clearInputAndClose(e)}>
                  <div className="rounded-full w-full h-[3px] bg-gray-900 absolute top-1/2 -translate-y-1/2 rotate-45"></div>
                  <div className="rounded-full h-full w-[3px] bg-gray-900 absolute left-1/2 -translate-x-1/2 rotate-45"></div>
               </div>
            </div>


         </div>
         <button
            className="p-2 text-lg btn-in-text_colors w-full "
            onClick={(e) => { setShowNewNumInput(true); }}>
            + add number
         </button>
      </div>
   )
}

export default GroupBox